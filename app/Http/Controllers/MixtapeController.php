<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\Mixtape;
use App\Services\ModerationService;
use Illuminate\Http\Request;

class MixtapeController extends Controller
{
    public function __construct(private ModerationService $moderation)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|min:3|max:80',
            'memory_ids'    => 'required|array|min:' . Mixtape::MIN_TRACKS . '|max:' . Mixtape::MAX_TRACKS,
            'memory_ids.*'  => 'string|distinct',
            'team_name'     => 'nullable|string|max:40',
            'team_color'    => 'nullable|string|max:20',
        ]);

        // Only approved memories can end up in a publicly shareable tape,
        // regardless of what IDs were posted.
        $memories = Memory::approved()->whereIn('id', $validated['memory_ids'])->get()->keyBy('id');

        if ($memories->count() < Mixtape::MIN_TRACKS) {
            return back()->withErrors(['memory_ids' => 'Not enough available memories to cut a tape.']);
        }

        $ipHash = hash_hmac('sha256', $request->ip() . now()->toDateString(), config('app.key'));

        // Rate limit: 3 mix tapes per IP per day
        $todayCount = Mixtape::where('ip_hash', $ipHash)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 3) {
            return back()->withErrors(['title' => 'You can only cut 3 mix tapes per day.']);
        }

        $status = $this->moderation->needsReview($validated['title']) ? 'pending' : 'approved';

        $mixtape = Mixtape::create([
            'title'      => $validated['title'],
            'team_name'  => $validated['team_name'] ?? null,
            'team_color' => $validated['team_color'] ?? null,
            'ip_hash'    => $ipHash,
            'status'     => $status,
        ]);

        $position = 0;
        foreach ($validated['memory_ids'] as $id) {
            if ($memories->has($id)) {
                $mixtape->memories()->attach($id, ['position' => $position++]);
            }
        }

        return redirect()->route('mixtapes.show', $mixtape);
    }

    public function show(Mixtape $mixtape)
    {
        if ($mixtape->status === 'rejected' && !auth()->check()) {
            abort(404);
        }

        if ($mixtape->status === 'approved') {
            $mixtape->load(['memories' => fn ($q) => $q->with('tags')]);
        }

        return view('mixtapes.show', compact('mixtape'));
    }
}
