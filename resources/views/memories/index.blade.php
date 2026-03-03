@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Basketball Memories</h1>
    <div class="divider"></div>
    <p>Anonymous stories from fans who lived them. No accounts, no likes, no noise.</p>
</div>

<div class="page-layout">

    {{-- Feed --}}
    <div>
        @if($memories->isEmpty())
            <div class="empty">
                <p>No memories yet. Be the first to share one.</p>
                <a href="/post" class="btn btn-primary">Share a memory</a>
            </div>
        @else
            @foreach($memories as $memory)
                <div class="memory-card">
                    <p class="memory-body">{{ $memory->body }}</p>

                    <div class="memory-tags">
                        @foreach($memory->tags as $tag)
                            <a href="/?tag={{ $tag->slug }}"
                               class="tag {{ $tag->type === 'team' ? 'tag-team' : '' }} {{ request('tag') === $tag->slug ? 'active' : '' }}">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>

                    <form action="/report/{{ $memory->id }}" method="POST">
                        @csrf
                        <button type="submit" class="report-btn">report</button>
                    </form>
                </div>
            @endforeach

            @if($memories->hasPages())
                <div class="pagination">
                    {{ $memories->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Glassmorphism Filter Sidebar --}}
    <aside class="filters">
        @if(isset($tags['team']))
            <h3>Teams</h3>
            @foreach($tags['team'] as $tag)
                <a href="/?tag={{ $tag->slug }}"
                   class="filter-link {{ request('tag') === $tag->slug ? 'active' : '' }}">
                    {{ $tag->name }}
                </a>
            @endforeach
        @endif

        @if(isset($tags['decade']))
            <h3>Era</h3>
            @foreach($tags['decade'] as $tag)
                <a href="/?tag={{ $tag->slug }}"
                   class="filter-link {{ request('tag') === $tag->slug ? 'active' : '' }}">
                    {{ $tag->name }}
                </a>
            @endforeach
        @endif

        @if(isset($tags['experience']))
            <h3>Experience</h3>
            @foreach($tags['experience'] as $tag)
                <a href="/?tag={{ $tag->slug }}"
                   class="filter-link {{ request('tag') === $tag->slug ? 'active' : '' }}">
                    {{ $tag->name }}
                </a>
            @endforeach
        @endif

        @if(request('tag'))
            <a href="/" class="filter-clear">✕ clear filter</a>
        @endif
    </aside>

</div>
@endsection
