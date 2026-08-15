<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    /**
     * Obvious slurs/spam markers checked when no OPENAI_API_KEY is
     * configured. Intentionally coarse — a stopgap, not real moderation.
     */
    private const BLOCKLIST = [
        'nigger', 'nigga', 'faggot', 'retard', 'kike', 'spic', 'chink',
        'porn', 'xxx', 'onlyfans',
        'viagra', 'crypto giveaway', 'bit.ly', 'click here to claim',
    ];

    /**
     * Decide whether a post needs manual review before going live.
     *
     * With OPENAI_API_KEY configured, uses OpenAI's moderation endpoint
     * and fails safe (holds for review) if that call errors. Without a
     * key, falls back to a local keyword blocklist so normal posts keep
     * publishing instantly instead of every post queuing for review.
     */
    public function needsReview(string $text): bool
    {
        $key = config('services.openai.key');

        if (!$key) {
            return $this->matchesBlocklist($text);
        }

        try {
            $response = Http::withToken($key)
                ->timeout(5)
                ->post('https://api.openai.com/v1/moderations', [
                    'model' => 'omni-moderation-latest',
                    'input' => $text,
                ]);

            if (!$response->successful()) {
                Log::warning('Moderation API request failed', ['status' => $response->status()]);
                return true;
            }

            return (bool) data_get($response->json(), 'results.0.flagged', true);
        } catch (\Throwable $e) {
            Log::warning('Moderation API request threw', ['message' => $e->getMessage()]);
            return true;
        }
    }

    private function matchesBlocklist(string $text): bool
    {
        $haystack = strtolower($text);

        foreach (self::BLOCKLIST as $term) {
            if (str_contains($haystack, $term)) {
                return true;
            }
        }

        return false;
    }
}
