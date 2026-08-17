<?php

namespace App\Http\Controllers;

use App\Models\Dunk;
use App\Services\ModerationService;
use Illuminate\Http\Request;

class DunkController extends Controller
{
    public function __construct(private ModerationService $moderation)
    {
    }

    public function index()
    {
        return Dunk::approved()->latest()->get(['id', 'body']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:10|max:500',
        ]);

        $ipHash = hash_hmac('sha256', $request->ip() . now()->toDateString(), config('app.key'));

        // Rate limit: 5 dunk submissions per IP per day
        $todayCount = Dunk::where('ip_hash', $ipHash)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 5) {
            return response()->json(['message' => 'You can only submit 5 dunks per day.'], 429);
        }

        $status = $this->moderation->needsReview($validated['body']) ? 'pending' : 'approved';

        $dunk = Dunk::create([
            'body'    => $validated['body'],
            'ip_hash' => $ipHash,
            'status'  => $status,
        ]);

        return response()->json([
            'id'     => $dunk->id,
            'body'   => $dunk->body,
            'status' => $dunk->status,
        ], 201);
    }
}
