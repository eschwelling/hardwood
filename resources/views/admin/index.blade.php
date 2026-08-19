@extends('layouts.app')

@section('content')
<h1 style="font-family:'Playfair Display',serif; font-weight:400; font-size:1.6rem; color:var(--amber); margin-bottom:2rem;">
    Moderation Queue
</h1>

@if($pending->isEmpty())
    <div class="empty">
        <p>Queue is clear. All good. 🏀</p>
    </div>
@else
    @foreach($pending as $memory)
        <div class="memory-card" style="padding: 1.5rem; background: var(--surface); border: 1px solid var(--border); margin-bottom: 1rem;">
            <p class="memory-body">{{ $memory->body }}</p>

            <div class="memory-tags" style="margin-bottom:1rem;">
                @foreach($memory->tags as $tag)
                    <span class="tag">{{ $tag->name }}</span>
                @endforeach
            </div>

            @if($memory->reports->count() > 0)
                <p style="font-size:0.78rem; color:#e87070; margin-bottom:1rem;">
                    {{ $memory->reports->count() }} report(s)
                </p>
            @endif

            <div style="display:flex; gap:1rem;">
                <form action="/admin/approve/{{ $memory->id }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.25rem; font-size:0.78rem;">Approve</button>
                </form>
                <form action="/admin/reject/{{ $memory->id }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1.25rem; font-size:0.78rem; border-color:#c84040; color:#e87070;">Reject</button>
                </form>
            </div>
        </div>
    @endforeach

    {{ $pending->links() }}
@endif

<h1 style="font-family:'Playfair Display',serif; font-weight:400; font-size:1.6rem; color:var(--amber); margin:3rem 0 2rem;">
    Pending Annotations
</h1>

@if($pendingAnnotations->isEmpty())
    <div class="empty">
        <p>No annotations waiting on review.</p>
    </div>
@else
    @foreach($pendingAnnotations as $annotation)
        <div class="memory-card" style="padding: 1.5rem; background: var(--surface); border: 1px solid var(--border); margin-bottom: 1rem;">
            <p class="memory-body" style="color:var(--text-muted); font-size:0.85rem; margin-bottom:0.75rem;">
                {{ \Illuminate\Support\Str::limit($annotation->memory->body, 160) }}
            </p>

            <p style="border-left:2px solid var(--amber); padding-left:0.75rem; margin-bottom:1rem;">
                {{ $annotation->body }}
            </p>

            <div style="display:flex; gap:1rem;">
                <form action="{{ route('admin.annotations.approve', $annotation) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.25rem; font-size:0.78rem;">Approve</button>
                </form>
                <form action="{{ route('admin.annotations.reject', $annotation) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1.25rem; font-size:0.78rem; border-color:#c84040; color:#e87070;">Reject</button>
                </form>
            </div>
        </div>
    @endforeach

    {{ $pendingAnnotations->links() }}
@endif

<h1 style="font-family:'Playfair Display',serif; font-weight:400; font-size:1.6rem; color:var(--amber); margin:3rem 0 2rem;">
    Pending Mix Tapes
</h1>

@if($pendingMixtapes->isEmpty())
    <div class="empty">
        <p>No mix tapes waiting on review.</p>
    </div>
@else
    @foreach($pendingMixtapes as $mixtape)
        <div class="memory-card" style="padding: 1.5rem; background: var(--surface); border: 1px solid var(--border); margin-bottom: 1rem;">
            <p class="memory-body" style="margin-bottom:0.5rem;">{{ $mixtape->title }}</p>

            <p style="font-size:0.78rem; color:var(--text-muted); margin-bottom:1rem;">
                {{ $mixtape->memories_count + $mixtape->dunks_count }} {{ \Illuminate\Support\Str::plural('track', $mixtape->memories_count + $mixtape->dunks_count) }}
                @if($mixtape->team_name)
                    · {{ $mixtape->team_name }}
                @endif
                · <a href="{{ route('mixtapes.show', $mixtape) }}" style="color:var(--amber);">preview</a>
            </p>

            <div style="display:flex; gap:1rem;">
                <form action="{{ route('admin.mixtapes.approve', $mixtape) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.25rem; font-size:0.78rem;">Approve</button>
                </form>
                <form action="{{ route('admin.mixtapes.reject', $mixtape) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1.25rem; font-size:0.78rem; border-color:#c84040; color:#e87070;">Reject</button>
                </form>
            </div>
        </div>
    @endforeach

    {{ $pendingMixtapes->links() }}
@endif

<h1 style="font-family:'Playfair Display',serif; font-weight:400; font-size:1.6rem; color:var(--amber); margin:3rem 0 2rem;">
    Pending Dunks
</h1>

@if($pendingDunks->isEmpty())
    <div class="empty">
        <p>No dunks waiting on review.</p>
    </div>
@else
    @foreach($pendingDunks as $dunk)
        <div class="memory-card" style="padding: 1.5rem; background: var(--surface); border: 1px solid var(--border); margin-bottom: 1rem;">
            <p class="memory-body">{{ $dunk->body }}</p>

            <div style="display:flex; gap:1rem;">
                <form action="{{ route('admin.dunks.approve', $dunk) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.25rem; font-size:0.78rem;">Approve</button>
                </form>
                <form action="{{ route('admin.dunks.reject', $dunk) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ghost" style="padding:0.5rem 1.25rem; font-size:0.78rem; border-color:#c84040; color:#e87070;">Reject</button>
                </form>
            </div>
        </div>
    @endforeach

    {{ $pendingDunks->links() }}
@endif
@endsection
