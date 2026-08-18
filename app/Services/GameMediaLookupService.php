<?php

namespace App\Services;

use App\Models\GameMedia;
use App\Models\Memory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameMediaLookupService
{
    /**
     * Look up a box score and highlight video for a memory's game, if it
     * has a date and a team tag to work from. Both external calls are
     * best-effort — a missing API key, a rate limit, or no match just
     * means that piece stays empty, never an error the poster sees.
     */
    public function lookup(Memory $memory, bool $force = false): void
    {
        if (!$memory->game_date) {
            return;
        }

        $team = $memory->tags()->where('type', 'team')->first();

        if (!$team) {
            return;
        }

        // A box score needs an exact day to match against; a looser
        // "month" or "year" precision (most people don't remember the
        // exact date of an old game) still gives video search enough
        // to work with, just a broader query.
        $precision = $memory->game_date_precision ?? 'day';
        $existing = $memory->gameMedia;

        // Only call out for pieces we don't already have. Both providers
        // are rate limited, so re-fetching something already on record is
        // a request that could have filled a genuine gap instead.
        $boxScore = ($precision === 'day' && ($force || !$existing?->box_score_summary))
            ? $this->findBoxScore($memory->game_date->toDateString(), $team->name)
            : null;

        $video = ($force || !$existing?->video_url)
            ? $this->findVideo($team->name, $memory->game_date, $precision, $boxScore['opponent'] ?? null)
            : null;

        // Stamped even when nothing was found, so a later backfill can tell
        // "we looked and came up empty" apart from "never looked" and not
        // re-query the same dead ends on every deploy.
        $payload = ['checked_at' => now()];

        if ($boxScore) {
            $payload['box_score_summary'] = $boxScore['summary'];
            $payload['box_score_url'] = $boxScore['url'];
        }

        if ($video) {
            $payload['video_title'] = $video['title'];
            $payload['video_url'] = $video['url'];
        }

        GameMedia::updateOrCreate(['memory_id' => $memory->id], $payload);
    }

    private function findBoxScore(string $date, string $teamName): ?array
    {
        $key = config('services.balldontlie.key');

        if (!$key) {
            return null;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $key])
                ->timeout(5)
                ->get('https://api.balldontlie.io/v1/games', ['dates[]' => $date]);
        } catch (\Throwable $e) {
            Log::warning('balldontlie request threw', ['message' => $e->getMessage()]);
            return null;
        }

        // The free tier allows 5 requests/minute. Getting throttled used to
        // be indistinguishable from "this game isn't in their data", which
        // made a pacing problem look like a coverage problem.
        if ($response->status() === 429) {
            Log::warning('balldontlie rate limit hit — box score skipped', ['date' => $date]);
            return null;
        }

        if (!$response->successful()) {
            Log::warning('balldontlie request failed', [
                'date' => $date,
                'status' => $response->status(),
            ]);
            return null;
        }

        foreach ($response->json('data') ?? [] as $game) {
            $home = $game['home_team']['full_name'] ?? null;
            $visitor = $game['visitor_team']['full_name'] ?? null;

            if ($home !== $teamName && $visitor !== $teamName) {
                continue;
            }

            $homeAbbrev = $game['home_team']['abbreviation'] ?? null;
            $compactDate = str_replace('-', '', $date);

            return [
                'summary'  => "{$visitor} {$game['visitor_team_score']} – {$home} {$game['home_team_score']}",
                'opponent' => $home === $teamName ? $visitor : $home,
                'url'      => $homeAbbrev ? "https://www.basketball-reference.com/boxscores/{$compactDate}0{$homeAbbrev}.html" : null,
            ];
        }

        return null;
    }

    private function findVideo(string $teamName, \Carbon\Carbon $date, string $precision, ?string $opponent): ?array
    {
        $key = config('services.youtube.key');

        if (!$key) {
            return null;
        }

        // Write the date the way a human titling a highlight reel would —
        // "May 26, 1987", not "1987-05-26". Nobody puts an ISO date in a
        // YouTube title, so searching one matches on the year at best.
        $when = match ($precision) {
            'day'   => $date->format('F j, Y'),
            'month' => $date->format('F Y'),
            default => (string) $date->year,
        };

        $query = $opponent
            ? "{$teamName} vs {$opponent} highlights {$when}"
            : "{$teamName} highlights {$when}";

        try {
            $response = Http::timeout(5)->get('https://www.googleapis.com/youtube/v3/search', [
                'part'       => 'snippet',
                'type'       => 'video',
                'maxResults' => 1,
                'q'          => $query,
                'key'        => $key,
            ]);
        } catch (\Throwable $e) {
            Log::warning('YouTube search request threw', ['message' => $e->getMessage()]);
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $item = $response->json('items.0');
        $videoId = $item['id']['videoId'] ?? null;

        if (!$videoId) {
            return null;
        }

        return [
            'title' => $item['snippet']['title'] ?? null,
            'url'   => "https://www.youtube.com/watch?v={$videoId}",
        ];
    }
}
