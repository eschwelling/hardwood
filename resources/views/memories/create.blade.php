@extends('layouts.app')

@section('content')

<section class="create-section">
    <div class="create-inner">

        <div class="create-header">
            <div class="create-eyebrow">Memory</div>
            <h1 class="create-title">Share<br><em>yours.</em></h1>
            <p class="create-sub">Anonymous. No account. Just the memory and the game.</p>
        </div>

        <form action="/post" method="POST" id="memory-form" class="create-form">
            @csrf

            <div class="form-field">
                <label class="field-label" for="body">Your memory</label>
                <textarea
                    id="body"
                    name="body"
                    rows="7"
                    placeholder="I was 12 years old, watching game 6 from the living room floor..."
                    minlength="50"
                    maxlength="500"
                >{{ old('body') }}</textarea>
                <div class="field-footer">
                    @error('body')
                        <span class="field-error">{{ $message }}</span>
                    @else
                        <span class="field-hint">50 – 500 characters</span>
                    @enderror
                    <span id="char-count" class="char-count">0</span>
                </div>
            </div>

            @error('tag_ids')
                <span class="field-error" style="display:block; margin-bottom:1.5rem;">{{ $message }}</span>
            @enderror

            @if(isset($tags['team']))
            <div class="form-field">
                <label class="field-label">Team(s)</label>
                <div class="checkbox-grid">
                    @foreach($tags['team'] as $tag)
                        <label class="check-label">
                            <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}>
                            <span class="check-text">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($tags['decade']))
            <div class="form-field">
                <label class="field-label">Era</label>
                <div class="checkbox-row">
                    @foreach($tags['decade'] as $tag)
                        <label class="check-label">
                            <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}>
                            <span class="check-text">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($tags['experience']))
            <div class="form-field">
                <label class="field-label">Experience</label>
                <div class="checkbox-row">
                    @foreach($tags['experience'] as $tag)
                        <label class="check-label">
                            <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}>
                            <span class="check-text">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submit-btn">Post memory</button>
                <a href="/" class="btn btn-ghost">Cancel</a>
                <span class="form-note">Anonymous · Max 3/day</span>
            </div>

        </form>

    </div>
</section>

<style>
    .create-section {
        min-height: 100vh;
        padding: 10rem 3rem 6rem;
        display: grid;
        grid-template-columns: 1fr;
        align-items: start;
    }

    .create-inner {
        max-width: 680px;
        margin: 0 auto;
        width: 100%;
    }

    .create-header {
        margin-bottom: 4rem;
    }

    .create-eyebrow {
        font-size: 0.68rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--amber);
        font-weight: 600;
        margin-bottom: 1rem;
        opacity: 0;
    }

    .create-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(3.5rem, 9vw, 7rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.02em;
        margin-bottom: 1.5rem;
        opacity: 0;
        transform: translateY(20px);
    }

    .create-title em {
        color: var(--amber);
        font-style: italic;
    }

    .create-sub {
        font-size: 0.9rem;
        color: var(--text-muted);
        opacity: 0;
    }

    .create-form {
        opacity: 0;
    }

    .form-field {
        margin-bottom: 2.5rem;
    }

    .field-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.6rem;
    }

    .field-hint {
        font-size: 0.72rem;
        color: var(--text-dim);
        letter-spacing: 0.06em;
    }

    .char-count {
        font-size: 0.72rem;
        color: var(--text-muted);
        font-variant-numeric: tabular-nums;
        transition: color 0.3s;
        letter-spacing: 0.06em;
    }

    .char-count.warning { color: var(--amber); }
    .char-count.danger { color: #e87070; }

    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.5rem 1.5rem;
    }

    .checkbox-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 1.5rem;
    }

    .check-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: none;
        user-select: none;
    }

    .check-label input[type="checkbox"] {
        width: 13px;
        height: 13px;
        accent-color: var(--amber);
        cursor: none;
        flex-shrink: 0;
    }

    .check-text {
        font-size: 0.82rem;
        color: var(--text-muted);
        transition: color 0.2s;
    }

    .check-label:hover .check-text,
    .check-label input:checked + .check-text {
        color: var(--amber);
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-top: 3rem;
        flex-wrap: wrap;
    }

    .form-note {
        font-size: 0.72rem;
        color: var(--text-dim);
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    @media (max-width: 700px) {
        .create-section { padding: 8rem 1.5rem 4rem; }
        .check-label { cursor: pointer; }
        .check-label input { cursor: pointer; }
    }
</style>

@endsection

@section('scripts')
<script>
    // Char counter
    const textarea = document.getElementById('body');
    const counter = document.getElementById('char-count');
    textarea.addEventListener('input', () => {
        const len = textarea.value.length;
        counter.textContent = len;
        counter.className = 'char-count';
        if (len > 470) counter.classList.add('danger');
        else if (len > 380) counter.classList.add('warning');
    });

    // Submit feedback
    document.getElementById('memory-form').addEventListener('submit', () => {
        const btn = document.getElementById('submit-btn');
        btn.textContent = 'Posting...';
        btn.style.opacity = '0.7';
    });

    // Entrance animations
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    tl.to('.create-eyebrow', { opacity: 1, duration: 0.6, delay: 0.2 })
      .to('.create-title', { opacity: 1, y: 0, duration: 0.7 }, '-=0.2')
      .to('.create-sub', { opacity: 1, duration: 0.5 }, '-=0.3')
      .to('.create-form', { opacity: 1, duration: 0.6 }, '-=0.2');
</script>
@endsection
