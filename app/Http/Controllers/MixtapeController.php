<?php

namespace App\Http\Controllers;

use App\Models\Dunk;
use App\Models\Memory;
use App\Models\Mixtape;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MixtapeController extends Controller
{
    public function __construct(private ModerationService $moderation)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|min:3|max:80',
            'tracks'     => 'required|array|min:' . Mixtape::MIN_TRACKS . '|max:' . Mixtape::MAX_TRACKS,
            'tracks.*'   => 'string|distinct',
            'team_name'  => 'nullable|string|max:40',
            'team_color' => 'nullable|string|max:20',
        ]);

        // Each track is "memory:<id>" or "dunk:<id>". Split them up so we
        // can look up only the approved rows — a publicly shareable tape
        // can never include something that hasn't cleared moderation,
        // regardless of what was posted.
        $memoryIds = [];
        $dunkIds = [];
        foreach ($validated['tracks'] as $track) {
            [$type, $id] = array_pad(explode(':', $track, 2), 2, null);
            if ($type === 'dunk') {
                $dunkIds[] = $id;
            } else {
                $memoryIds[] = $id;
            }
        }

        $memories = Memory::approved()->whereIn('id', $memoryIds)->get()->keyBy('id');
        $dunks = Dunk::approved()->whereIn('id', $dunkIds)->get()->keyBy('id');

        $rows = [];
        foreach ($validated['tracks'] as $track) {
            [$type, $id] = array_pad(explode(':', $track, 2), 2, null);
            if ($type === 'dunk' && $dunks->has($id)) {
                $rows[] = ['type' => Dunk::class, 'id' => $id];
            } elseif ($type !== 'dunk' && $memories->has($id)) {
                $rows[] = ['type' => Memory::class, 'id' => $id];
            }
        }

        if (count($rows) < Mixtape::MIN_TRACKS) {
            return back()->withErrors(['tracks' => 'Not enough available tracks to cut a tape.']);
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

        $now = now();
        DB::table('mixtape_trackables')->insert(array_map(fn ($row, $position) => [
            'mixtape_id'     => $mixtape->id,
            'trackable_type' => $row['type'],
            'trackable_id'   => $row['id'],
            'position'       => $position,
            'created_at'     => $now,
            'updated_at'     => $now,
        ], $rows, array_keys($rows)));

        return redirect()->route('mixtapes.show', $mixtape);
    }

    public function show(Mixtape $mixtape)
    {
        if ($mixtape->status === 'rejected' && !auth()->check()) {
            abort(404);
        }

        if ($mixtape->status === 'approved') {
            $mixtape->load(['memories.tags', 'dunks']);
        }

        return view('mixtapes.show', compact('mixtape'));
    }
}
