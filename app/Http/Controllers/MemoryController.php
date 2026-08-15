<?php

namespace App\Http\Controllers;

use App\Data\NbaOnThisDay;
use App\Models\Annotation;
use App\Models\Memory;
use App\Models\Resonate;
use App\Models\Tag;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemoryController extends Controller
{
    public function __construct(private ModerationService $moderation)
    {
    }

    public function index(Request $request)
    {
        $isAdmin = auth()->check();

        // Admin mode sees every status (pending/rejected included) and every
        // annotation, so moderation can happen inline without leaving the feed.
        $query = ($isAdmin ? Memory::query() : Memory::approved())
            ->with([
                'tags',
                'resonates',
                'annotations' => fn ($q) => $isAdmin ? $q->orderBy('start_offset') : $q->approved()->orderBy('start_offset'),
            ])
            ->latest();

        // Filter by tag slug if provided
        if ($request->has('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        }

        $memories = $query->paginate(20);
        $tags = Tag::orderBy('type')->orderBy('name')->get()->groupBy('type');
        $onThisDay = NbaOnThisDay::forDate(now());

        return view('memories.index', compact('memories', 'tags', 'onThisDay'));
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

        $status = $this->moderation->needsReview($validated['body']) ? 'pending' : 'approved';

        $memory = Memory::create([
            'body'    => $validated['body'],
            'ip_hash' => $ipHash,
            'status'  => $status,
        ]);

        $memory->tags()->attach($validated['tag_ids']);

        if ($status === 'pending') {
            return redirect()->route('memories.index')
                ->with('success', 'Thanks — your memory is in for a quick review before it goes live. 🏀');
        }

        // Echo: surface one other memory that shares a tag, so the poster
        // sees they're not the only one who remembers this.
        $echo = Memory::approved()
            ->where('id', '!=', $memory->id)
            ->whereHas('tags', fn($q) => $q->whereIn('tags.id', $validated['tag_ids']))
            ->with('tags')
            ->inRandomOrder()
            ->first();

        return redirect()->route('memories.index')
            ->with('success', 'Your memory has been shared. 🏀')
            ->with('echo', $echo);
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

    public function resonate(Request $request, Memory $memory)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', Resonate::TYPES),
        ]);

        // Deterministic per-IP hash (unlike Hash::make) so the unique
        // constraint can actually catch a repeat tap from the same visitor.
        $ipHash = hash_hmac('sha256', $request->ip(), config('app.key'));

        // updateOrCreate rather than firstOrCreate: tapping a different
        // reaction switches your reaction instead of being a no-op.
        $memory->resonates()->updateOrCreate(
            ['ip_hash' => $ipHash],
            ['type' => $validated['type']]
        );

        return response()->json(['ok' => true]);
    }

    public function annotate(Request $request, Memory $memory)
    {
        $validated = $request->validate([
            'start_offset' => 'required|integer|min:0',
            'end_offset'   => 'required|integer|gt:start_offset',
            'body'         => 'required|string|min:2|max:280',
        ]);

        if ($validated['end_offset'] > mb_strlen($memory->body)) {
            abort(422, 'Annotation range is out of bounds.');
        }

        $ipHash = hash_hmac('sha256', $request->ip() . now()->toDateString(), config('app.key'));

        // Rate limit: 10 annotations per IP per day
        $todayCount = Annotation::where('ip_hash', $ipHash)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 10) {
            return response()->json(['error' => 'You can only add 10 annotations per day.'], 429);
        }

        $status = $this->moderation->needsReview($validated['body']) ? 'pending' : 'approved';

        $annotation = $memory->annotations()->create([
            'start_offset' => $validated['start_offset'],
            'end_offset'   => $validated['end_offset'],
            'body'         => $validated['body'],
            'ip_hash'      => $ipHash,
            'status'       => $status,
        ]);

        return response()->json([
            'ok'     => true,
            'status' => $status,
            'annotation' => $status === 'approved' ? [
                'id'            => $annotation->id,
                'start_offset'  => $annotation->start_offset,
                'end_offset'    => $annotation->end_offset,
                'body'          => $annotation->body,
            ] : null,
        ]);
    }
}
