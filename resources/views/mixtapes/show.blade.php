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
            @php($trackCount = $mixtape->memories->count() + $mixtape->dunks->count())
            <p class="mixtape-cover-meta">
                {{ $trackCount }} {{ \Illuminate\Support\Str::plural('track', $trackCount) }}
                @if($mixtape->team_name)
                    · mixed to the tune of {{ $mixtape->team_name }}
                @endif
            </p>
        </div>

        <ol class="mixtape-page-tracklist">
            @foreach($mixtape->tracks() as $i => $track)
                @php($isDunk = $track instanceof \App\Models\Dunk)
                <li class="mixtape-page-track">
                    <span class="mixtape-page-track-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="mixtape-page-track-body">
                        @if($isDunk)
                            <span class="mixtape-page-track-kind">🏀 Dunk Archive</span>
                        @endif
                        <p class="mixtape-page-track-text">{{ $track->body }}</p>
                        @unless($isDunk)
                            <div class="memory-tags">
                                @foreach($track->tags as $tag)
                                    <span class="tag {{ $tag->type === 'team' ? 'tag-team' : '' }}">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endunless
                    </div>
                </li>
            @endforeach
        </ol>

        <div class="mixtape-page-actions">
            <a href="/" class="btn btn-ghost mixtape-back">← Back to the feed</a>
            <button type="button" id="mixtape-download-btn" class="btn btn-ghost">Download cover</button>
        </div>
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

    .mixtape-page-track-kind {
        display: block;
        font-size: 0.65rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--amber);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .mixtape-page-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .mixtape-back {
        display: inline-block;
    }
</style>

@endsection

@if($mixtape->status === 'approved')
@section('scripts')
<script>
    // Mix tape cover card — same idea as the per-memory canvas card, but
    // laid out like a tracklist/liner-notes sleeve instead of a single quote.
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mixtape-download-btn');
        if (!btn) return;

        const title = {!! json_encode($mixtape->title) !!};
        const teamName = {!! json_encode($mixtape->team_name) !!};
        const tracks = {!! json_encode($mixtape->tracks()->map(fn ($t) => \Illuminate\Support\Str::limit($t->body, 70))->values()) !!};

        async function downloadMixtapeCard() {
            await Promise.all([
                document.fonts.load('italic 700 60px "Playfair Display"'),
                document.fonts.load('italic 700 26px "Playfair Display"'),
                document.fonts.load('700 22px "Inter"'),
                document.fonts.load('500 24px "Inter"'),
            ]);

            const W = 1080, H = 1350;
            const canvas = document.createElement('canvas');
            canvas.width = W;
            canvas.height = H;
            const ctx = canvas.getContext('2d');

            const rootStyle = getComputedStyle(document.documentElement);
            const amberRgb = rootStyle.getPropertyValue('--amber-rgb').trim() || '200,135,42';
            const amberHex = rootStyle.getPropertyValue('--amber').trim() || '#c8872a';

            ctx.fillStyle = '#080706';
            ctx.fillRect(0, 0, W, H);

            const glow = ctx.createRadialGradient(W / 2, H * 0.22, 0, W / 2, H * 0.22, W * 0.7);
            glow.addColorStop(0, `rgba(${amberRgb},0.18)`);
            glow.addColorStop(1, `rgba(${amberRgb},0)`);
            ctx.fillStyle = glow;
            ctx.fillRect(0, 0, W, H);

            ctx.strokeStyle = `rgba(${amberRgb},0.35)`;
            ctx.lineWidth = 2;
            ctx.strokeRect(40, 40, W - 80, H - 80);

            ctx.textAlign = 'center';
            ctx.fillStyle = amberHex;
            ctx.font = '700 22px Inter, sans-serif';
            ctx.fillText('🎧  M I X   T A P E', W / 2, 150);

            ctx.fillStyle = '#f0e8d8';
            ctx.font = 'italic 700 60px "Playfair Display", serif';
            const maxWidth = W - 180;
            const titleWords = title.split(' ');
            const titleLines = [];
            let tLine = '';
            titleWords.forEach((word) => {
                const test = tLine ? `${tLine} ${word}` : word;
                if (tLine && ctx.measureText(test).width > maxWidth) {
                    titleLines.push(tLine);
                    tLine = word;
                } else {
                    tLine = test;
                }
            });
            if (tLine) titleLines.push(tLine);
            titleLines.slice(0, 3).forEach((l, i) => ctx.fillText(l, W / 2, 250 + i * 68));

            let y = 250 + titleLines.slice(0, 3).length * 68 + 30;

            if (teamName) {
                ctx.font = '500 24px Inter, sans-serif';
                ctx.fillStyle = '#6a6050';
                ctx.fillText(`mixed to the tune of ${teamName}`, W / 2, y);
                y += 60;
            }

            y += 20;
            ctx.textAlign = 'left';
            const listX = 110;
            const listWidth = W - 220;

            tracks.slice(0, 8).forEach((track, i) => {
                ctx.font = 'italic 700 26px "Playfair Display", serif';
                ctx.fillStyle = amberHex;
                const num = String(i + 1).padStart(2, '0');
                ctx.fillText(num, listX, y);

                ctx.font = '400 24px Inter, sans-serif';
                ctx.fillStyle = '#f0e8d8';
                let text = track;
                while (ctx.measureText(text).width > listWidth - 70 && text.length > 10) {
                    text = text.slice(0, -1);
                }
                if (text.length < track.length) text += '…';
                ctx.fillText(text, listX + 70, y);

                y += 56;
            });

            if (tracks.length > 8) {
                ctx.font = '500 20px Inter, sans-serif';
                ctx.fillStyle = '#6a6050';
                ctx.fillText(`+ ${tracks.length - 8} more`, listX + 70, y);
            }

            ctx.textAlign = 'center';
            ctx.font = '700 20px Inter, sans-serif';
            ctx.fillStyle = '#6a6050';
            ctx.fillText('R A F T E R S', W / 2, H - 90);

            return new Promise((resolve) => {
                canvas.toBlob((blob) => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'rafters-mixtape.png';
                    a.click();
                    URL.revokeObjectURL(url);
                    resolve();
                }, 'image/png');
            });
        }

        btn.addEventListener('click', () => {
            const original = btn.textContent;
            btn.textContent = '...';
            downloadMixtapeCard().finally(() => { btn.textContent = original; });
        });
    });
</script>
@endsection
@endif
