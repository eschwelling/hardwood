@extends('layouts.app')

@section('content')
<div class="admin-login-wrap">
    <h1 class="admin-login-title">Admin</h1>

    @if($errors->any())
        <p class="admin-login-error">{{ $errors->first() }}</p>
    @endif

    <form action="{{ route('admin.login') }}" method="POST" class="admin-login-form">
        @csrf
        <label class="admin-login-label" for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="admin-login-input">

        <label class="admin-login-label" for="password">Password</label>
        <input type="password" id="password" name="password" required class="admin-login-input">

        <button type="submit" class="btn btn-primary admin-login-submit">Enter admin mode</button>
    </form>
</div>

<style>
    .admin-login-wrap {
        max-width: 340px;
        margin: 4rem auto;
    }

    .admin-login-title {
        font-family: 'Playfair Display', serif;
        font-weight: 400;
        font-size: 1.6rem;
        color: var(--amber);
        margin-bottom: 2rem;
        text-align: center;
    }

    .admin-login-error {
        color: #e87070;
        font-size: 0.8rem;
        margin-bottom: 1.25rem;
        text-align: center;
    }

    .admin-login-form {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .admin-login-label {
        font-size: 0.68rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-top: 1rem;
    }

    .admin-login-input {
        background: var(--surface);
        border: 1px solid var(--border2);
        border-radius: 4px;
        color: var(--text);
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        padding: 0.65rem 0.75rem;
    }

    .admin-login-input:focus {
        outline: none;
        border-color: var(--amber);
    }

    .admin-login-submit {
        margin-top: 1.75rem;
        width: 100%;
        text-align: center;
        border: none;
        cursor: pointer;
    }
</style>
@endsection
