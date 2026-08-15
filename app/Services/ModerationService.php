<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    /**
     * Check text against OpenAI's moderation endpoint.
     *
     * Returns true if the content should be held for manual review —
     * either because it was flagged, or because moderation couldn't be
     * performed (missing key, API error, timeout). Never silently lets
     * unmoderated content go straight to the public feed.
     */
    public function needsReview(string $text): bool
    {
        $key = config('services.openai.key');

        if (!$key) {
            Log::warning('Moderation skipped: OPENAI_API_KEY not configured. Holding for manual review.');
            return true;
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
}
