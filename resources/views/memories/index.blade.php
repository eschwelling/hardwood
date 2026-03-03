@extends('layouts.app')

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="hero-inner">
        <div class="hero-eyebrow">Est. 2026 &nbsp;·&nbsp; Anonymous &nbsp;·&nbsp; No accounts</div>
        <h1 class="hero-title">
            <span class="hero-word">Hard</span><span class="hero-word hero-word--amber">wood</span>
        </h1>
        <p class="hero-sub">Basketball memories from fans who lived them.<br>No names. No likes. Just the game.</p>
        <a href="/post" class="btn btn-primary hero-btn">Share a memory</a>
    </div>
    <div class="hero-scroll-hint">
        <span>Scroll</span>
        <div class="hero-scroll-line"></div>
    </div>
</section>

{{-- Tag filter bar --}}
<section class="feed-section">
    <div class="feed-inner">

        <div class="tag-filter-bar">
            <div class="tag-filter-group">
                <a href="/" class="tag {{ !request('tag') ? 'active' : '' }}">All</a>
            </div>
            <div class="tag-filter-divider"></div>
            @if(isset($tags['team']))
                <div class="tag-filter-group">
                    @foreach($tags['team'] as $tag)
                        <a href="/?tag={{ $tag->slug }}" class="tag tag-team {{ request('tag') === $tag->slug ? 'active' : '' }}">{{ $tag->name }}</a>
                    @endforeach
                </div>
                <div class="tag-filter-divider"></div>
            @endif
            @if(isset($tags['decade']))
                <div class="tag-filter-group">
                    @foreach($tags['decade'] as $tag)
                        <a href="/?tag={{ $tag->slug }}" class="tag {{ request('tag') === $tag->slug ? 'active' : '' }}">{{ $tag->name }}</a>
                    @endforeach
                </div>
                <div class="tag-filter-divider"></div>
            @endif
            @if(isset($tags['experience']))
                <div class="tag-filter-group">
                    @foreach($tags['experience'] as $tag)
                        <a href="/?tag={{ $tag->slug }}" class="tag {{ request('tag') === $tag->slug ? 'active' : '' }}">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif
            @if(request('tag'))
                <div class="tag-filter-divider"></div>
                <a href="/" class="tag" style="color:var(--amber); border-color:var(--amber);">✕ clear</a>
            @endif
        </div>

        {{-- Feed --}}
        @if($memories->isEmpty())
            <div class="empty">
                <p>No memories yet. Be the first to share one.</p>
                <a href="/post" class="btn btn-primary">Share a memory</a>
            </div>
        @else
            <div class="memories-feed">
                @foreach($memories as $memory)
                    <article class="memory-card">
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
                    </article>
                @endforeach
            </div>

            @if($memories->hasPages())
                <div class="pagination">{{ $memories->links() }}</div>
            @endif
        @endif

    </div>
</section>

<style>
    /* Hero */
    .hero {
        height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        padding: 0 2rem;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 20%;
        left: 50%;
        transform: translateX(-50%);
        width: 800px;
        height: 600px;
        background: radial-gradient(ellipse, rgba(200,135,42,0.07) 0%, transparent 65%);
        pointer-events: none;
    }

    .hero-inner {
        position: relative;
        z-index: 1;
    }

    .hero-eyebrow {
        font-size: 0.68rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 2rem;
        opacity: 0;
    }

    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(5rem, 16vw, 14rem);
        font-weight: 900;
        line-height: 0.9;
        letter-spacing: -0.02em;
        margin-bottom: 2rem;
        display: flex;
        justify-content: center;
        gap: 0.05em;
    }

    .hero-word {
        display: inline-block;
        opacity: 0;
        transform: translateY(40px);
    }

    .hero-word--amber { color: var(--amber); }

    .hero-sub {
        font-size: 1rem;
        color: var(--text-muted);
        font-weight: 300;
        line-height: 1.8;
        margin-bottom: 3rem;
        opacity: 0;
    }

    .hero-btn {
        opacity: 0;
        transform: translateY(10px);
    }

    .hero-scroll-hint {
        position: absolute;
        bottom: 3rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        opacity: 0;
    }

    .hero-scroll-hint span {
        font-size: 0.65rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--text-muted);
        font-weight: 500;
    }

    .hero-scroll-line {
        width: 1px;
        height: 60px;
        background: linear-gradient(180deg, var(--amber), transparent);
        animation: scrollPulse 2s ease-in-out infinite;
    }

    @keyframes scrollPulse {
        0%, 100% { opacity: 0.3; transform: scaleY(1); }
        50% { opacity: 1; transform: scaleY(1.1); }
    }

    /* Feed section */
    .feed-section {
        min-height: 100vh;
        padding: 0 3rem 5rem;
    }

    .feed-inner {
        max-width: 780px;
        margin: 0 auto;
    }

    .memories-feed {
        padding-left: 3rem;
        border-left: 1px solid var(--border);
    }

    @media (max-width: 700px) {
        .hero-title { font-size: clamp(4rem, 20vw, 8rem); }
        .feed-section { padding: 0 1.5rem 4rem; }
        .memories-feed { padding-left: 0; border-left: none; }
    }
</style>

@endsection

@section('scripts')
<script>
    // Hero entrance animations
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    tl.to('.hero-eyebrow', { opacity: 1, duration: 0.8, delay: 0.3 })
      .to('.hero-word', { opacity: 1, y: 0, duration: 0.8, stagger: 0.15 }, '-=0.4')
      .to('.hero-sub', { opacity: 1, duration: 0.7 }, '-=0.3')
      .to('.hero-btn', { opacity: 1, y: 0, duration: 0.6 }, '-=0.3')
      .to('.hero-scroll-hint', { opacity: 1, duration: 0.6 }, '-=0.2');
</script>
@endsection
