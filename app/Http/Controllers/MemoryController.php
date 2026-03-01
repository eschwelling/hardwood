<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Memory::approved()
            ->with('tags')
            ->latest();

        // Filter by tag slug if provided
        if ($request->has('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        }

        $memories = $query->paginate(20);
        $tags = Tag::orderBy('type')->orderBy('name')->get()->groupBy('type');

        return view('memories.index', compact('memories', 'tags'));
    }

    public function create()
    {
        $tags = Tag::orderBy('type')->orderBy('name')->get()->groupBy('type');
        return view('memories.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body'    => 'required|string|min:50|max:500',
            'tag_ids' => 'required|array|min:1|max:5',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        // Rate limit: 3 posts per IP per day
        $ipHash = Hash::make($request->ip() . now()->toDateString());
        $todayCount = Memory::where('ip_hash', $ipHash)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 3) {
            return back()->withErrors(['body' => 'You can only post 3 memories per day.']);
        }

        $memory = Memory::create([
            'body'    => $validated['body'],
            'ip_hash' => $ipHash,
            'status'  => 'approved', // TODO: add OpenAI moderation
        ]);

        $memory->tags()->attach($validated['tag_ids']);

        return redirect()->route('memories.index')
            ->with('success', 'Your memory has been shared. 🏀');
    }

    public function report(Request $request, Memory $memory)
    {
        $ipHash = Hash::make($request->ip());

        // Don't allow duplicate reports from same IP
        $alreadyReported = $memory->reports()->where('ip_hash', $ipHash)->exists();

        if (!$alreadyReported) {
            $memory->reports()->create([
                'reason'  => $request->reason,
                'ip_hash' => $ipHash,
            ]);

            // Auto-hide if 5+ reports
            if ($memory->reports()->count() >= 5) {
                $memory->update(['status' => 'pending']);
            }
        }

        return back()->with('success', 'Thanks for the report.');
    }
}
