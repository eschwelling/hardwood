<?php

namespace App\Console\Commands;

use App\Models\Memory;
use App\Services\GameMediaLookupService;
use Illuminate\Console\Command;

/**
 * Backfills box scores and highlight videos for memories that never had
 * the lookup run against them.
 *
 * The lookup normally fires when a memory is posted or approved, so
 * anything that entered the database another way — the seeder, a direct
 * insert — has no media attached. This also covers memories that were
 * posted while an API key was missing or a provider was down.
 */
class BackfillGameMedia extends Command
{
    protected $signature = 'memories:backfill-media
                            {--force : Re-run every lookup, even for media already on record}
                            {--limit=100 : Maximum number of memories to process in one run}
                            {--sleep=0 : Seconds to wait between memories, to stay under provider rate limits}
                            {--recheck-after=14 : Days before a fruitless lookup is attempted again}';

    protected $description = 'Look up box scores and highlight videos for memories missing them';

    public function handle(GameMediaLookupService $lookup): int
    {
        if (!config('services.balldontlie.key') && !config('services.youtube.key')) {
            $this->warn('Neither BALLDONTLIE_API_KEY nor YOUTUBE_API_KEY is set — nothing to look up.');

            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $sleep = (int) $this->option('sleep');

        $memories = Memory::approved()
            ->whereNotNull('game_date')
            ->with('tags', 'gameMedia')
            ->get();

        if (!$force) {
            $memories = $memories->filter(
                fn (Memory $memory) => $this->needsLookup($memory)
            );
        }

        $memories = $memories->take((int) $this->option('limit'))->values();

        if ($memories->isEmpty()) {
            $this->info('No memories need a media lookup.');

            return self::SUCCESS;
        }

        $this->info("Looking up media for {$memories->count()} memories...");

        $attached = 0;

        foreach ($memories as $i => $memory) {
            // Providers are rate limited per minute, so pace the run rather
            // than firing the whole batch at once and getting most of it
            // throttled into silence.
            if ($sleep > 0 && $i > 0) {
                sleep($sleep);
            }

            $lookup->lookup($memory, $force);

            $media = $memory->fresh('gameMedia')->gameMedia;

            if ($media && ($media->box_score_summary || $media->video_url)) {
                $attached++;
                $this->line(sprintf(
                    '  ✓ %s%s',
                    $media->video_url ? 'video ' : '',
                    $media->box_score_summary ? 'box score' : ''
                ));
            } else {
                $this->line('  · nothing found');
            }
        }

        $this->info("Done. {$attached} of {$memories->count()} memories have media.");

        return self::SUCCESS;
    }

    /**
     * A memory needs a lookup if it has never been checked, or if it's
     * still missing a piece that a configured provider could supply and
     * enough time has passed to be worth asking again.
     */
    private function needsLookup(Memory $memory): bool
    {
        $media = $memory->gameMedia;

        if (!$media) {
            return true;
        }

        // A row with no checked_at predates that column, so it's due for a
        // look; otherwise wait out the recheck window before asking again.
        $dueForRecheck = !$media->checked_at
            || $media->checked_at->diffInDays(now()) >= (int) $this->option('recheck-after');

        if (!$dueForRecheck) {
            return false;
        }

        $precision = $memory->game_date_precision ?? 'day';

        $missingBoxScore = $precision === 'day'
            && !$media->box_score_summary
            && config('services.balldontlie.key');

        $missingVideo = !$media->video_url && config('services.youtube.key');

        return $missingBoxScore || $missingVideo;
    }
}
