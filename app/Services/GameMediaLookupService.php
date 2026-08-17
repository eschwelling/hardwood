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
    public function lookup(Memory $memory): void
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

        $boxScore = $precision === 'day'
            ? $this->findBoxScore($memory->game_date->toDateString(), $team->name)
            : null;

        $video = $this->findVideo($team->name, $memory->game_date, $precision, $boxScore['opponent'] ?? null);

        if (!$boxScore && !$video) {
            return;
        }

        GameMedia::updateOrCreate(
            ['memory_id' => $memory->id],
            [
                'box_score_summary' => $boxScore['summary'] ?? null,
                'box_score_url'     => $boxScore['url'] ?? null,
                'video_title'       => $video['title'] ?? null,
                'video_url'         => $video['url'] ?? null,
            ]
        );
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

        if (!$response->successful()) {
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

        $when = match ($precision) {
            'day'   => $date->toDateString(),
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
