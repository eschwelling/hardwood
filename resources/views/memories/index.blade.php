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
        <div class="hero-actions">
            <a href="/post" class="btn btn-primary hero-btn">Share a memory</a>
            @if($memories->isNotEmpty())
                <button type="button" id="reading-mode-btn" class="btn btn-ghost hero-btn">Reading mode</button>
            @endif
        </div>
    </div>
    <div class="hero-scroll-hint">
        <span>Scroll</span>
        <div class="hero-scroll-line"></div>
    </div>
</section>

{{-- Tag filter bar --}}
<section class="feed-section">
    <div class="feed-inner">

        {{-- On this day --}}
        <div class="otd-banner">
            <span class="otd-label">{{ $onThisDay['exact'] ? 'On this day' : 'From the archive' }}</span>
            <p class="otd-text">
                @if($onThisDay['exact'])<strong>{{ $onThisDay['year'] }}</strong> — @endif{{ $onThisDay['text'] }}
                @if($onThisDay['tag'])
                    <a href="/?tag={{ $onThisDay['tag'] }}" class="otd-link">Browse these memories →</a>
                @endif
            </p>
        </div>

        {{-- Echo: a related memory surfaced right after posting --}}
        @if($echo = session('echo'))
            <div class="echo-card">
                <span class="echo-label">Someone else remembers this too</span>
                <p class="echo-body">{{ $echo->body }}</p>
                <div class="memory-tags">
                    @foreach($echo->tags as $tag)
                        <span class="tag {{ $tag->type === 'team' ? 'tag-team' : '' }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

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
                @php $reactionEmoji = ['fire' => '🔥', 'goat' => '🐐', 'cry' => '😭', 'hype' => '🙌']; @endphp
                @foreach($memories as $memory)
                    @php $reactionCounts = $memory->resonates->countBy('type'); @endphp
                    <article class="memory-card">
                        <p class="memory-body" data-memory-id="{{ $memory->id }}">{!! \App\Support\AnnotationRenderer::render($memory) !!}</p>
                        <div class="memory-tags">
                            @foreach($memory->tags as $tag)
                                <a href="/?tag={{ $tag->slug }}"
                                   class="tag {{ $tag->type === 'team' ? 'tag-team' : '' }} {{ request('tag') === $tag->slug ? 'active' : '' }}">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                        <div class="memory-actions">
                            <div class="reaction-group" data-memory-id="{{ $memory->id }}">
                                @foreach($reactionEmoji as $type => $emoji)
                                    <button type="button" class="reaction-btn" data-type="{{ $type }}" title="{{ ucfirst($type) }}">
                                        <span class="reaction-emoji">{{ $emoji }}</span>
                                        <span class="reaction-count">{{ ($reactionCounts[$type] ?? 0) > 0 ? $reactionCounts[$type] : '' }}</span>
                                    </button>
                                @endforeach
                            </div>
                            <button type="button" class="card-btn">card</button>
                            <form action="/report/{{ $memory->id }}" method="POST" class="report-form">
                                @csrf
                                <button type="submit" class="report-btn">report</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($memories->hasPages())
                <div class="pagination">{{ $memories->links() }}</div>
            @endif
        @endif

    </div>
</section>

{{-- Reading mode --}}
<div id="reading-overlay" class="reading-overlay" aria-hidden="true">
    <button type="button" id="reading-close" class="reading-close" aria-label="Close reading mode">✕</button>
    <div class="reading-counter" id="reading-counter"></div>
    <div class="reading-stage">
        <button type="button" id="reading-prev" class="reading-nav" aria-label="Previous memory">‹</button>
        <div class="reading-content">
            <p id="reading-body" class="reading-body"></p>
            <div id="reading-tags" class="reading-tags"></div>
        </div>
        <button type="button" id="reading-next" class="reading-nav" aria-label="Next memory">›</button>
    </div>
    <div class="reading-hint">← → to navigate · Esc to close</div>
</div>

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

    .hero-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
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

    /* On this day */
    .otd-banner {
        padding: 1.1rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border2);
        border-left: 2px solid var(--amber);
        background: var(--surface);
    }

    .otd-label {
        display: block;
        font-size: 0.65rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--amber);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .otd-text {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.7;
    }

    .otd-text strong { color: var(--text); }

    .otd-link {
        color: var(--amber);
        text-decoration: none;
        white-space: nowrap;
        margin-left: 0.35rem;
    }

    .otd-link:hover { color: var(--amber-light); }

    /* Echo card */
    .echo-card {
        padding: 1.75rem;
        margin-bottom: 2rem;
        background: var(--amber-dim);
        border: 1px solid var(--border2);
        border-left: 2px solid var(--amber);
        animation: slideDown 0.5s ease;
    }

    .echo-label {
        display: block;
        font-size: 0.65rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--amber);
        font-weight: 600;
        margin-bottom: 0.9rem;
    }

    .echo-body {
        font-family: 'Playfair Display', serif;
        font-style: italic;
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--text);
        margin-bottom: 1rem;
    }

    /* Memory actions */
    .memory-actions {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-top: 1rem;
    }

    .report-form { margin: 0; }
    .memory-actions .report-btn { margin: 0; }

    .resonate-btn, .card-btn {
        background: none;
        border: none;
        color: var(--text-dim);
        font-size: 0.65rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        cursor: none;
        padding: 0;
        display: inline-flex;
        align-items: center;
        transition: color 0.2s;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
    }

    .card-btn:hover { color: var(--amber); }

    .resonate-btn { color: var(--text-muted); }
    .resonate-btn:hover { color: var(--amber); }

    .resonate-btn.lit {
        color: var(--amber-light);
        animation: resonatePulse 0.6s ease;
    }

    .resonate-btn.lit .flame-icon {
        filter: drop-shadow(0 0 6px var(--amber-glow));
    }

    /* Reactions */
    .reaction-group {
        display: inline-flex;
        align-items: center;
        gap: 0.85rem;
    }

    .reaction-btn {
        background: none;
        border: none;
        padding: 0;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        opacity: 0.55;
        filter: grayscale(0.6);
        transition: opacity 0.2s, filter 0.2s, transform 0.2s;
    }

    .reaction-btn:hover {
        opacity: 0.9;
        filter: grayscale(0.2);
    }

    .reaction-btn.active {
        opacity: 1;
        filter: grayscale(0);
        transform: scale(1.08);
    }

    .reaction-emoji {
        font-size: 0.95rem;
        line-height: 1;
    }

    .reaction-count {
        font-size: 0.65rem;
        color: var(--text-muted);
        min-width: 0.7em;
        font-weight: 500;
    }

    /* Annotations */
    .annotation {
        background: var(--amber-dim);
        border-bottom: 1px dashed var(--amber);
        color: var(--amber-light);
        cursor: help;
        position: relative;
    }

    .annotation:hover,
    .annotation:focus {
        background: var(--amber-glow);
        outline: none;
    }

    .annotation:hover::after,
    .annotation:focus::after {
        content: attr(data-note);
        position: absolute;
        left: 50%;
        bottom: 100%;
        transform: translateX(-50%);
        margin-bottom: 8px;
        background: var(--surface2);
        border: 1px solid var(--border2);
        color: var(--text);
        padding: 0.55rem 0.8rem;
        border-radius: 4px;
        font-size: 0.72rem;
        line-height: 1.45;
        white-space: normal;
        width: max-content;
        max-width: 240px;
        z-index: 60;
        box-shadow: 0 10px 24px rgba(0,0,0,0.45);
        text-transform: none;
        letter-spacing: normal;
    }

    .annotation-popover {
        position: absolute;
        z-index: 900;
        transform: translateX(-50%);
        background: var(--surface2);
        border: 1px solid var(--border2);
        border-radius: 6px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.5);
        padding: 0.4rem;
    }

    .annotation-trigger {
        background: var(--amber);
        color: var(--bg);
        border: none;
        border-radius: 4px;
        padding: 0.4rem 0.75rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        white-space: nowrap;
    }

    .annotation-form {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 220px;
    }

    .annotation-form textarea {
        background: var(--surface);
        border: 1px solid var(--border2);
        border-radius: 4px;
        color: var(--text);
        font-family: 'Inter', sans-serif;
        font-size: 0.78rem;
        padding: 0.5rem;
        resize: none;
    }

    .annotation-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .annotation-form-actions button {
        font-family: 'Inter', sans-serif;
        font-size: 0.68rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border: none;
        border-radius: 4px;
        padding: 0.4rem 0.7rem;
        cursor: pointer;
    }

    .btn-annotation-submit {
        background: var(--amber);
        color: var(--bg);
        font-weight: 600;
    }

    .btn-annotation-cancel {
        background: none;
        color: var(--text-muted);
    }

    .annotation-status {
        font-size: 0.68rem;
        color: var(--text-muted);
        padding: 0.3rem 0.1rem;
    }

    @keyframes resonatePulse {
        0% { transform: scale(1); }
        40% { transform: scale(1.5); }
        100% { transform: scale(1); }
    }

    /* Reading mode */
    .reading-overlay {
        position: fixed;
        inset: 0;
        z-index: 800;
        background: rgba(6,5,4,0.97);
        backdrop-filter: blur(10px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease;
    }

    .reading-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    .reading-close {
        position: absolute;
        top: 2rem;
        right: 2.5rem;
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.1rem;
        cursor: none;
        transition: color 0.2s;
    }

    .reading-close:hover { color: var(--amber); }

    .reading-counter {
        position: absolute;
        top: 2.1rem;
        left: 2.5rem;
        font-size: 0.7rem;
        letter-spacing: 0.14em;
        color: var(--text-dim);
        font-variant-numeric: tabular-nums;
    }

    .reading-stage {
        display: flex;
        align-items: center;
        gap: 2.5rem;
        max-width: 900px;
        width: 100%;
        padding: 0 2rem;
    }

    .reading-nav {
        background: none;
        border: 1px solid var(--border2);
        color: var(--text-muted);
        width: 42px;
        height: 42px;
        border-radius: 50%;
        font-size: 1.3rem;
        cursor: none;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    .reading-nav:hover { color: var(--amber); border-color: var(--amber); }

    .reading-content {
        flex: 1;
        text-align: center;
        opacity: 1;
        transition: opacity 0.25s ease;
    }

    .reading-body {
        font-family: 'Playfair Display', serif;
        font-style: italic;
        font-size: clamp(1.3rem, 3vw, 2rem);
        line-height: 1.7;
        color: var(--text);
        margin-bottom: 1.75rem;
    }

    .reading-tags {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .reading-hint {
        position: absolute;
        bottom: 2.5rem;
        font-size: 0.68rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--text-dim);
    }

    @media (max-width: 700px) {
        .hero-title { font-size: clamp(4rem, 20vw, 8rem); }
        .feed-section { padding: 0 1.5rem 4rem; }
        .memories-feed { padding-left: 0; border-left: none; }
        .reading-stage { gap: 1rem; }
        .reading-nav { width: 34px; height: 34px; font-size: 1.1rem; }
        .resonate-btn, .card-btn, .report-btn { cursor: pointer; }
        .reading-close, .reading-nav { cursor: pointer; }
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

<script>
    // Reactions — pick one of a few specific reactions per memory (BeReal-
    // style RealMoji, not a single generic like). Tapping a different one
    // switches your reaction; the choice is remembered locally per memory.
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const storeKey = 'hardwood-reactions';
        const mine = JSON.parse(localStorage.getItem(storeKey) || '{}');

        document.querySelectorAll('.reaction-group').forEach((group) => {
            const memoryId = group.dataset.memoryId;
            const buttons = [...group.querySelectorAll('.reaction-btn')];

            const markActive = (type) => {
                buttons.forEach((b) => b.classList.toggle('active', b.dataset.type === type));
            };

            if (mine[memoryId]) markActive(mine[memoryId]);

            buttons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const type = btn.dataset.type;
                    if (mine[memoryId] === type) return;

                    const previous = mine[memoryId];
                    const countEl = btn.querySelector('.reaction-count');
                    countEl.textContent = String((parseInt(countEl.textContent || '0', 10) || 0) + 1);

                    if (previous) {
                        const prevBtn = buttons.find((b) => b.dataset.type === previous);
                        const prevCountEl = prevBtn?.querySelector('.reaction-count');
                        if (prevCountEl) {
                            const next = (parseInt(prevCountEl.textContent || '0', 10) || 0) - 1;
                            prevCountEl.textContent = next > 0 ? String(next) : '';
                        }
                    }

                    mine[memoryId] = type;
                    localStorage.setItem(storeKey, JSON.stringify(mine));
                    markActive(type);

                    fetch(`/resonate/${memoryId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ type }),
                    }).catch(() => {});
                });
            });
        });
    });
</script>

<script>
    // Annotations — highlight a phrase inside a memory to attach context,
    // Genius-style. Selecting text shows a small "+ annotate" popover.
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const popover = document.createElement('div');
        popover.className = 'annotation-popover';
        popover.style.display = 'none';
        popover.innerHTML = `
            <button type="button" class="annotation-trigger">+ annotate</button>
            <form class="annotation-form" style="display:none;">
                <textarea maxlength="280" rows="2" placeholder="Add context, a source, a story..."></textarea>
                <div class="annotation-form-actions">
                    <button type="button" class="btn-annotation-cancel">Cancel</button>
                    <button type="submit" class="btn-annotation-submit">Post</button>
                </div>
            </form>
        `;
        document.body.appendChild(popover);

        const trigger = popover.querySelector('.annotation-trigger');
        const form = popover.querySelector('.annotation-form');
        const textarea = popover.querySelector('textarea');

        let active = null; // { memoryId, start, end }

        function hidePopover() {
            popover.style.display = 'none';
            trigger.style.display = '';
            form.style.display = 'none';
            textarea.value = '';
            active = null;
        }

        function textOffsets(container, range) {
            const preRange = document.createRange();
            preRange.selectNodeContents(container);
            preRange.setEnd(range.startContainer, range.startOffset);
            const start = preRange.toString().length;
            return { start, end: start + range.toString().length };
        }

        function handleSelection(el) {
            const selection = window.getSelection();
            if (!selection || selection.isCollapsed || selection.rangeCount === 0) return;

            const text = selection.toString().trim();
            if (text.length < 2) return;

            const range = selection.getRangeAt(0);
            if (!el.contains(range.commonAncestorContainer)) return;

            const offsets = textOffsets(el, range);
            active = { memoryId: el.dataset.memoryId, start: offsets.start, end: offsets.end };

            const rect = range.getBoundingClientRect();
            popover.style.left = `${rect.left + window.scrollX + rect.width / 2}px`;
            popover.style.top = `${rect.top + window.scrollY - 42}px`;
            popover.style.display = 'block';
            trigger.style.display = '';
            form.style.display = 'none';
        }

        document.querySelectorAll('.memory-body').forEach((el) => {
            el.addEventListener('mouseup', () => handleSelection(el));
            el.addEventListener('touchend', () => handleSelection(el));
        });

        trigger.addEventListener('click', () => {
            trigger.style.display = 'none';
            form.style.display = 'flex';
            textarea.focus();
        });

        popover.querySelector('.btn-annotation-cancel').addEventListener('click', hidePopover);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!active) return;

            const body = textarea.value.trim();
            if (!body) return;

            const { memoryId, start, end } = active;

            try {
                const res = await fetch(`/annotate/${memoryId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ start_offset: start, end_offset: end, body }),
                });

                if (res.status === 429) {
                    form.innerHTML = '<p class="annotation-status">Daily annotation limit reached.</p>';
                    setTimeout(hidePopover, 2000);
                    return;
                }

                const data = await res.json();

                if (data.status === 'approved') {
                    window.getSelection().removeAllRanges();
                    hidePopover();
                    location.reload();
                    return;
                }

                form.innerHTML = '<p class="annotation-status">Thanks — held for a quick review. 🏀</p>';
                setTimeout(hidePopover, 2000);
            } catch (err) {
                hidePopover();
            }
        });

        document.addEventListener('mousedown', (e) => {
            if (!popover.contains(e.target)) hidePopover();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') hidePopover();
        });
    });
</script>

<script>
    // Memory card generator — draws the memory to a shareable PNG client-side
    async function downloadMemoryCard(body, tags) {
        await Promise.all([
            document.fonts.load('italic 900 54px "Playfair Display"'),
            document.fonts.load('700 22px "Inter"'),
            document.fonts.load('600 20px "Inter"'),
        ]);

        const W = 1080, H = 1350;
        const canvas = document.createElement('canvas');
        canvas.width = W;
        canvas.height = H;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#080706';
        ctx.fillRect(0, 0, W, H);

        const glow = ctx.createRadialGradient(W / 2, H * 0.32, 0, W / 2, H * 0.32, W * 0.7);
        glow.addColorStop(0, 'rgba(200,135,42,0.16)');
        glow.addColorStop(1, 'rgba(200,135,42,0)');
        ctx.fillStyle = glow;
        ctx.fillRect(0, 0, W, H);

        ctx.strokeStyle = 'rgba(200,135,42,0.35)';
        ctx.lineWidth = 2;
        ctx.strokeRect(40, 40, W - 80, H - 80);

        ctx.textAlign = 'center';
        ctx.fillStyle = '#c8872a';
        ctx.font = '700 22px Inter, sans-serif';
        ctx.fillText('H A R D W O O D', W / 2, 140);

        ctx.fillStyle = '#f0e8d8';
        ctx.font = 'italic 900 54px "Playfair Display", serif';
        const maxWidth = W - 200;
        const words = body.split(' ');
        const lines = [];
        let line = '';
        words.forEach((word) => {
            const test = line ? `${line} ${word}` : word;
            if (line && ctx.measureText(test).width > maxWidth) {
                lines.push(line);
                line = word;
            } else {
                line = test;
            }
        });
        if (line) lines.push(line);

        const lineHeight = 74;
        const startY = H / 2 - ((lines.length - 1) * lineHeight) / 2;
        lines.forEach((l, i) => ctx.fillText(l, W / 2, startY + i * lineHeight));

        if (tags.length) {
            ctx.font = '600 20px Inter, sans-serif';
            ctx.fillStyle = '#6a6050';
            let shown = tags;
            let label = shown.map((t) => t.toUpperCase()).join('   ·   ');
            while (shown.length > 1 && ctx.measureText(label).width > maxWidth) {
                shown = shown.slice(0, -1);
                label = `${shown.map((t) => t.toUpperCase()).join('   ·   ')}   ···`;
            }
            ctx.fillText(label, W / 2, H - 130);
        }

        return new Promise((resolve) => {
            canvas.toBlob((blob) => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'hardwood-memory.png';
                a.click();
                URL.revokeObjectURL(url);
                resolve();
            }, 'image/png');
        });
    }

    document.querySelectorAll('.card-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.memory-card');
            const body = card.querySelector('.memory-body').textContent.trim();
            const tags = [...card.querySelectorAll('.memory-tags a')].map((t) => t.textContent.trim());
            const original = btn.textContent;
            btn.textContent = '...';
            downloadMemoryCard(body, tags).finally(() => { btn.textContent = original; });
        });
    });
</script>

<script>
    // Reading mode — a fullscreen, one-at-a-time pass through the current feed
    (function () {
        const overlay = document.getElementById('reading-overlay');
        if (!overlay) return;

        const contentEl = document.querySelector('.reading-content');
        const bodyEl = document.getElementById('reading-body');
        const tagsEl = document.getElementById('reading-tags');
        const counterEl = document.getElementById('reading-counter');
        const openBtn = document.getElementById('reading-mode-btn');
        const closeBtn = document.getElementById('reading-close');
        const prevBtn = document.getElementById('reading-prev');
        const nextBtn = document.getElementById('reading-next');

        const memories = [...document.querySelectorAll('.memory-card')].map((card) => ({
            body: card.querySelector('.memory-body').textContent.trim(),
            tags: [...card.querySelectorAll('.memory-tags a')].map((t) => t.textContent.trim()),
        }));

        let index = 0;

        function render() {
            const m = memories[index];
            contentEl.style.opacity = 0;
            setTimeout(() => {
                bodyEl.textContent = m.body;
                tagsEl.innerHTML = '';
                m.tags.forEach((t) => {
                    const span = document.createElement('span');
                    span.className = 'tag';
                    span.textContent = t;
                    tagsEl.appendChild(span);
                });
                counterEl.textContent = `${index + 1} / ${memories.length}`;
                contentEl.style.opacity = 1;
            }, 150);
        }

        function open() {
            if (!memories.length) return;
            index = 0;
            render();
            overlay.classList.add('open');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function close() {
            overlay.classList.remove('open');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function next() { index = (index + 1) % memories.length; render(); }
        function prev() { index = (index - 1 + memories.length) % memories.length; render(); }

        openBtn?.addEventListener('click', open);
        closeBtn.addEventListener('click', close);
        nextBtn.addEventListener('click', next);
        prevBtn.addEventListener('click', prev);

        document.addEventListener('keydown', (e) => {
            if (!overlay.classList.contains('open')) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowRight') next();
            if (e.key === 'ArrowLeft') prev();
        });

        let touchX = null;
        overlay.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; });
        overlay.addEventListener('touchend', (e) => {
            if (touchX === null) return;
            const dx = e.changedTouches[0].clientX - touchX;
            if (Math.abs(dx) > 50) (dx < 0 ? next() : prev());
            touchX = null;
        });
    })();
</script>
@endsection
