@extends('layouts.app')

@section('content')
<div style="max-width: 600px;">

    <h1 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 400; color: var(--amber); margin-bottom: 0.5rem;">
        Share a memory
    </h1>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2.5rem;">
        Anonymous. No account. Just the memory and the game.
    </p>

    <form action="/post" method="POST">
        @csrf

        {{-- Memory body --}}
        <div style="margin-bottom: 2rem;">
            <label for="body">Your memory</label>
            <textarea
                id="body"
                name="body"
                rows="6"
                placeholder="I was 12 years old, watching game 6 from the living room floor..."
                minlength="50"
                maxlength="500"
            >{{ old('body') }}</textarea>
            <div style="display:flex; justify-content:space-between; margin-top:0.4rem;">
                @error('body')
                    <span class="field-error">{{ $message }}</span>
                @else
                    <span></span>
                @enderror
                <span style="font-size:0.75rem; color:var(--text-dim);" id="char-count">0 / 500</span>
            </div>
        </div>

        {{-- Tags --}}
        @error('tag_ids')
            <span class="field-error" style="display:block; margin-bottom:1rem;">{{ $message }}</span>
        @enderror

        @if(isset($tags['team']))
        <div style="margin-bottom: 1.75rem;">
            <label>Team(s)</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                @foreach($tags['team'] as $tag)
                    <label style="text-transform:none; letter-spacing:0; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:0.35rem; color:var(--text-muted);">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}
                            style="width:auto; accent-color:var(--amber);">
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        @if(isset($tags['decade']))
        <div style="margin-bottom: 1.75rem;">
            <label>Era</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                @foreach($tags['decade'] as $tag)
                    <label style="text-transform:none; letter-spacing:0; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:0.35rem; color:var(--text-muted);">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}
                            style="width:auto; accent-color:var(--amber);">
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        @if(isset($tags['experience']))
        <div style="margin-bottom: 2.5rem;">
            <label>Experience</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                @foreach($tags['experience'] as $tag)
                    <label style="text-transform:none; letter-spacing:0; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:0.35rem; color:var(--text-muted);">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}
                            style="width:auto; accent-color:var(--amber);">
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        <div style="display:flex; align-items:center; gap:1.5rem;">
            <button type="submit" class="btn btn-primary">Post memory</button>
            <a href="/" class="btn btn-ghost">Cancel</a>
        </div>

        <p style="font-size:0.78rem; color:var(--text-dim); margin-top:1.5rem;">
            Anonymous. No tracking. No account required. Max 3 memories per day.
        </p>
    </form>
</div>

<script>
    const textarea = document.getElementById('body');
    const counter = document.getElementById('char-count');
    textarea.addEventListener('input', () => {
        counter.textContent = textarea.value.length + ' / 500';
        counter.style.color = textarea.value.length > 450 ? 'var(--amber)' : 'var(--text-dim)';
    });
</script>
@endsection
