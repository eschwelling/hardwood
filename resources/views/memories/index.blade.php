@extends('layouts.app')

@section('content')
<div class="page-layout">

    {{-- Feed --}}
    <div>
        @if($memories->isEmpty())
            <div class="empty">
                <p>No memories yet. <a href="/post">Be the first to share one.</a></p>
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

            {{-- Pagination --}}
            @if($memories->hasPages())
                <div class="pagination">
                    {{ $memories->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Filters --}}
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
            <a href="/" class="filter-link" style="margin-top:1.5rem; color: var(--amber);">✕ clear filter</a>
        @endif
    </aside>

</div>
@endsection
