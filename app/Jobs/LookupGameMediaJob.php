<?php

namespace App\Jobs;

use App\Models\Memory;
use App\Services\GameMediaLookupService;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Dispatched with ->afterResponse() rather than queued — this app runs a
 * single php -S process with no queue worker, so afterResponse (runs once
 * the HTTP response has already been sent to the client) is what keeps the
 * external API calls in lookup() from adding perceived latency to the
 * request that triggered them.
 */
class LookupGameMediaJob
{
    use Dispatchable;

    public function __construct(private Memory $memory)
    {
    }

    public function handle(GameMediaLookupService $lookup): void
    {
        $lookup->lookup($this->memory);
    }
}
