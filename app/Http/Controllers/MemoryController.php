<?php

namespace App\Http\Controllers;

use App\Data\NbaOnThisDay;
use App\Jobs\LookupGameMediaJob;
use App\Models\Annotation;
use App\Models\Memory;
use App\Models\Resonate;
use App\Models\Tag;
use App\Models\Venue;
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
                'venues',
                'resonates',
                'annotations' => fn ($q) => $isAdmin ? $q->orderBy('start_offset') : $q->approved()->orderBy('start_offset'),
                'gameMedia',
            ])
            ->latest();

        // Filter by tag slug if provided
        if ($request->has('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        }

        // Venues aren't tags (they carry lat/lng, tags don't), so they get
        // their own filter param rather than overloading ?tag=.
        if ($request->filled('venue')) {
            $query->whereHas('venues', fn ($q) => $q->where('slug', $request->query('venue')));
        }

        // Paired with a tag filter (from the "N others remember this game"
        // link below), narrows further to memories dated to that exact day.
        if ($request->filled('game_date')) {
            $query->whereDate('game_date', $request->query('game_date'));
        }

        $memories = $query->paginate(20);
        $tags = Tag::orderBy('type')->orderBy('name')->get()->groupBy('type');
        $venues = Venue::orderBy('name')->get();
        $onThisDay = NbaOnThisDay::forDate(now());
        $gameMateCounts = $this->gameMateCounts($memories->getCollection());

        // Postgres won't let HAVING reference a withCount() alias (unlike
        // MySQL), so filter out zero-count rows in PHP instead — ORDER BY
        // desc already guarantees anything with real resonates sorts above
        // the zero-count ones, so this can't drop a memory that belongs here.
        $leaderboard = Memory::approved()
            ->withCount('resonates')
            ->orderByDesc('resonates_count')
            ->take(5)
            ->get()
            ->filter(fn ($memory) => $memory->resonates_count > 0)
            ->values();

        return view('memories.index', compact('memories', 'tags', 'venues', 'onThisDay', 'leaderboard', 'gameMateCounts'));
    }

    /**
     * "N others remember this game" — only meaningful for exact-day dates
     * paired with a team tag; a looser month/year precision is too fuzzy
     * to treat as the same game. Batched into one query per page load
     * rather than a query per card.
     */
    private function gameMateCounts($pageMemories): array
    {
        $datedMemories = $pageMemories->filter(fn ($memory) => $memory->game_date_precision === 'day');

        if ($datedMemories->isEmpty()) {
            return [];
        }

        $dates = $datedMemories->pluck('game_date')->map(fn ($d) => $d->toDateString())->unique();
        $teamIds = $datedMemories
            ->flatMap(fn ($memory) => $memory->tags->where('type', 'team')->pluck('id'))
            ->unique();

        if ($teamIds->isEmpty()) {
            return [];
        }

        $candidates = Memory::approved()
            ->whereIn('game_date', $dates)
            ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $teamIds))
            ->with('tags')
            ->get(['id', 'game_date']);

        $counts = [];

        foreach ($datedMemories as $memory) {
            $teamId = optional($memory->tags->firstWhere('type', 'team'))->id;

            if (!$teamId) {
                continue;
            }

            $count = $candidates
                ->where('id', '!=', $memory->id)
                ->filter(fn ($c) => $c->game_date->isSameDay($memory->game_date) && $c->tags->contains('id', $teamId))
                ->count();

            if ($count > 0) {
                $counts[$memory->id] = $count;
            }
        }

        return $counts;
    }

    public function create()
    {
        $tags = Tag::orderBy('type')->orderBy('name')->get()->groupBy('type');
        $venues = Venue::orderBy('name')->get();
        return view('memories.create', compact('tags', 'venues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body'        => 'required|string|min:50|max:500',
            'tag_ids'     => 'required|array|min:1|max:5',
            'tag_ids.*'   => 'exists:tags,id',
            'venue_ids'   => 'nullable|array|max:2',
            'venue_ids.*' => 'exists:venues,id',
            'game_year'   => 'nullable|integer|min:1946|max:' . now()->year,
            'game_month'  => 'nullable|integer|between:1,12',
            'game_day'    => 'nullable|integer|between:1,31',
        ]);

        [$gameDate, $gameDatePrecision] = $this->resolveGameDate($validated);

        if ($gameDate && $gameDate->isFuture()) {
            return back()->withErrors(['game_year' => 'That date hasn\'t happened yet.'])->withInput();
        }

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
            'body'                 => $validated['body'],
            'game_date'            => $gameDate,
            'game_date_precision'  => $gameDatePrecision,
            'ip_hash'              => $ipHash,
            'status'               => $status,
        ]);

        $memory->tags()->attach($validated['tag_ids']);

        // The venue <select> has no `multiple` attribute (most people were
        // only at one arena), so its unselected default option still
        // submits an empty value — filter it out before attaching.
        $venueIds = array_filter($validated['venue_ids'] ?? []);
        if ($venueIds) {
            $memory->venues()->attach($venueIds);
        }

        if ($status === 'approved' && $memory->game_date) {
            LookupGameMediaJob::dispatch($memory)->afterResponse();
        }

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

    /**
     * Builds a game_date from whatever precision the poster gave —
     * full date, month + year, or just a year — since most people
     * don't remember the exact day of an old game. Box score lookups
     * need day precision, but video search can work off a looser one.
     */
    private function resolveGameDate(array $validated): array
    {
        if (empty($validated['game_year'])) {
            return [null, null];
        }

        $year = (int) $validated['game_year'];
        $month = $validated['game_month'] ? (int) $validated['game_month'] : null;
        $day = $validated['game_day'] ? (int) $validated['game_day'] : null;

        if ($month && $day && checkdate($month, $day, $year)) {
            return [\Carbon\Carbon::create($year, $month, $day), 'day'];
        }

        if ($month) {
            return [\Carbon\Carbon::create($year, $month, 1), 'month'];
        }

        return [\Carbon\Carbon::create($year, 1, 1), 'year'];
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
