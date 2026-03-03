@extends('layouts.app')

@section('content')
<div style="max-width: 620px;">

    <div class="page-header">
        <h1>Share a memory</h1>
        <div class="divider"></div>
        <p>Anonymous. No account. Just the memory and the game.</p>
    </div>

    <form action="/post" method="POST" id="memory-form">
        @csrf

        {{-- Memory body --}}
        <div style="margin-bottom: 2rem;">
            <label class="field-label" for="body">Your memory</label>
            <textarea
                id="body"
                name="body"
                rows="6"
                placeholder="I was 12 years old, watching game 6 from the living room floor..."
                minlength="50"
                maxlength="500"
            >{{ old('body') }}</textarea>
            <div style="display:flex; justify-content:space-between; margin-top:0.5rem; align-items:center;">
                @error('body')
                    <span class="field-error">{{ $message }}</span>
                @else
                    <span></span>
                @enderror
                <span class="char-counter" id="char-count" style="font-size:0.75rem; color:var(--text-dim); font-variant-numeric: tabular-nums;">0 / 500</span>
            </div>
        </div>

        {{-- Tags --}}
        @error('tag_ids')
            <span class="field-error" style="display:block; margin-bottom:1rem;">{{ $message }}</span>
        @enderror

        @if(isset($tags['team']))
        <div style="margin-bottom: 1.75rem;">
            <label class="field-label">Team(s)</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem 1rem;">
                @foreach($tags['team'] as $tag)
                    <label class="check-label">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}>
                        <span>{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        @if(isset($tags['decade']))
        <div style="margin-bottom: 1.75rem;">
            <label class="field-label">Era</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem 1rem;">
                @foreach($tags['decade'] as $tag)
                    <label class="check-label">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}>
                        <span>{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        @if(isset($tags['experience']))
        <div style="margin-bottom: 2.5rem;">
            <label class="field-label">Experience</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem 1rem;">
                @foreach($tags['experience'] as $tag)
                    <label class="check-label">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}>
                        <span>{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        <div style="display:flex; align-items:center; gap:1.5rem;">
            <button type="submit" class="btn btn-primary" id="submit-btn">
                Post memory
            </button>
            <a href="/" class="btn btn-ghost">Cancel</a>
        </div>

        <p style="font-size:0.75rem; color:var(--text-dim); margin-top:1.5rem; line-height:1.7;">
            Anonymous. No tracking. No account required.<br>Max 3 memories per day.
        </p>
    </form>
</div>

<style>
    .check-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.82rem;
        color: var(--text-muted);
        cursor: pointer;
        transition: color 0.2s;
        user-select: none;
    }

    .check-label:hover { color: var(--text); }

    .check-label input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: var(--amber);
        cursor: pointer;
    }

    .check-label input:checked + span { color: var(--amber); }

    .char-counter { transition: color 0.3s; }
    .char-counter.warning { color: var(--amber) !important; }
    .char-counter.danger { color: #e87070 !important; }
</style>

<script>
    const textarea = document.getElementById('body');
    const counter = document.getElementById('char-count');
    const submitBtn = document.getElementById('submit-btn');

    textarea.addEventListener('input', () => {
        const len = textarea.value.length;
        counter.textContent = len + ' / 500';
        counter.className = 'char-counter';
        if (len > 470) counter.classList.add('danger');
        else if (len > 400) counter.classList.add('warning');
    });

    // Submit animation
    document.getElementById('memory-form').addEventListener('submit', () => {
        submitBtn.textContent = 'Posting...';
        submitBtn.style.opacity = '0.7';
    });

    // Animate form in
    gsap.fromTo('.page-header', { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.5 });
    gsap.fromTo('form > div', { opacity: 0, y: 10 }, {
        opacity: 1, y: 0, duration: 0.4, stagger: 0.07, delay: 0.2
    });
</script>
@endsection
