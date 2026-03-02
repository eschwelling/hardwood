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
@endsection
