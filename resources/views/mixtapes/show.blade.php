@extends('layouts.app')

@section('content')

@if($mixtape->status === 'pending')
    <div class="mixtape-page">
        <span class="mixtape-cover-label">🎧 Mix Tape</span>
        <h1 class="mixtape-page-title">{{ $mixtape->title }}</h1>
        <p class="mixtape-pending-text">This tape is in for a quick review before the link is shareable. Check back soon — nobody else can see it yet.</p>
        <a href="/" class="btn btn-ghost mixtape-back">← Back to the feed</a>
    </div>
@elseif($mixtape->status === 'rejected')
    <div class="mixtape-page">
        <span class="mixtape-cover-label">🎧 Mix Tape</span>
        <h1 class="mixtape-page-title">{{ $mixtape->title }}</h1>
        <p class="mixtape-pending-text">This tape didn't clear review, so it's no longer shareable.</p>
        <a href="/" class="btn btn-ghost mixtape-back">← Back to the feed</a>
    </div>
@else
    <div class="mixtape-page">
        <div class="mixtape-cover">
            <span class="mixtape-cover-label">🎧 Mix Tape</span>
            <h1 class="mixtape-page-title">{{ $mixtape->title }}</h1>
            <p class="mixtape-cover-meta">
                {{ $mixtape->memories->count() }} {{ \Illuminate\Support\Str::plural('track', $mixtape->memories->count()) }}
                @if($mixtape->team_name)
                    · mixed to the tune of {{ $mixtape->team_name }}
                @endif
            </p>
        </div>

        <ol class="mixtape-page-tracklist">
            @foreach($mixtape->memories as $i => $memory)
                <li class="mixtape-page-track">
                    <span class="mixtape-page-track-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="mixtape-page-track-body">
                        <p class="mixtape-page-track-text">{{ $memory->body }}</p>
                        <div class="memory-tags">
                            @foreach($memory->tags as $tag)
                                <span class="tag {{ $tag->type === 'team' ? 'tag-team' : '' }}">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>

        <a href="/" class="btn btn-ghost mixtape-back">← Back to the feed</a>
    </div>
@endif

<style>
    .mixtape-page {
        max-width: 640px;
        margin: 6rem auto 4rem;
        padding: 0 2rem;
    }

    .mixtape-cover {
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--border2);
    }

    .mixtape-cover-label {
        display: block;
        font-size: 0.68rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--amber);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .mixtape-page-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-style: italic;
        font-size: clamp(2rem, 5vw, 3rem);
        color: var(--text);
        line-height: 1.15;
        margin-bottom: 0.75rem;
    }

    .mixtape-cover-meta {
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .mixtape-pending-text {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.7;
        margin-bottom: 2rem;
        max-width: 480px;
    }

    .mixtape-page-tracklist {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .mixtape-page-track {
        display: flex;
        gap: 1.5rem;
    }

    .mixtape-page-track-num {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-style: italic;
        font-size: 1.1rem;
        color: var(--amber);
        flex-shrink: 0;
        width: 2rem;
    }

    .mixtape-page-track-text {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--text);
        margin-bottom: 0.85rem;
    }

    .mixtape-back {
        display: inline-block;
    }
</style>

@endsection
