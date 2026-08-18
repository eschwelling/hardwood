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
                            {--force : Re-run for memories that already have media attached}
                            {--limit=100 : Maximum number of memories to process in one run}';

    protected $description = 'Look up box scores and highlight videos for memories missing them';

    public function handle(GameMediaLookupService $lookup): int
    {
        if (!config('services.balldontlie.key') && !config('services.youtube.key')) {
            $this->warn('Neither BALLDONTLIE_API_KEY nor YOUTUBE_API_KEY is set — nothing to look up.');

            return self::SUCCESS;
        }

        $query = Memory::approved()
            ->whereNotNull('game_date')
            ->with('tags');

        // Without --force, only touch memories that have no media row at
        // all. That keeps repeat runs cheap and, more importantly, stops
        // us burning YouTube quota re-searching for clips that genuinely
        // don't exist. Use --force after adding a new provider key.
        if (!$this->option('force')) {
            $query->whereDoesntHave('gameMedia');
        }

        $memories = $query->limit((int) $this->option('limit'))->get();

        if ($memories->isEmpty()) {
            $this->info('No memories need a media lookup.');

            return self::SUCCESS;
        }

        $this->info("Looking up media for {$memories->count()} memories...");

        $attached = 0;

        foreach ($memories as $memory) {
            $lookup->lookup($memory);

            $media = $memory->fresh('gameMedia')->gameMedia;

            if ($media && ($media->box_score_summary || $media->video_url)) {
                $attached++;
                $this->line(sprintf(
                    '  ✓ %s%s',
                    $media->video_url ? 'video ' : '',
                    $media->box_score_summary ? 'box score' : ''
                ));
            }
        }

        $this->info("Done. Attached media to {$attached} of {$memories->count()} memories.");

        return self::SUCCESS;
    }
}
