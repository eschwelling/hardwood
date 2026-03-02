<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardwood — Basketball Memories</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0f0e0c;
            --surface: #1a1814;
            --border: #2a2620;
            --amber: #c8872a;
            --amber-light: #e8a84a;
            --text: #e8e0d0;
            --text-muted: #7a7060;
            --text-dim: #4a4438;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-weight: 300;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Nav */
        nav {
            border-bottom: 1px solid var(--border);
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 860px;
            margin: 0 auto;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--amber);
            text-decoration: none;
            letter-spacing: 0.02em;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            transition: color 0.2s;
        }

        .nav-link:hover { color: var(--amber); }

        /* Layout */
        .container {
            max-width: 860px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* Flash messages */
        .flash {
            background: var(--surface);
            border: 1px solid var(--amber);
            border-left: 3px solid var(--amber);
            color: var(--amber-light);
            padding: 0.875rem 1.25rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .flash-error {
            border-color: #c84040;
            color: #e87070;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.75rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--amber);
            color: var(--bg);
        }

        .btn-primary:hover {
            background: var(--amber-light);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            border-color: var(--amber);
            color: var(--amber);
        }

        /* Forms */
        label {
            display: block;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        textarea, input {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            padding: 1rem 1.25rem;
            outline: none;
            transition: border-color 0.2s;
            resize: none;
        }

        textarea:focus, input:focus {
            border-color: var(--amber);
        }

        textarea::placeholder {
            color: var(--text-dim);
            font-style: italic;
        }

        .field-error {
            color: #e87070;
            font-size: 0.8rem;
            margin-top: 0.4rem;
        }

        /* Memory card */
        .memory-card {
            padding: 2rem 0;
            border-bottom: 1px solid var(--border);
        }

        .memory-card:last-child {
            border-bottom: none;
        }

        .memory-body {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text);
            margin-bottom: 1.25rem;
        }

        .memory-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .tag {
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            border: 1px solid var(--border);
            padding: 0.25rem 0.6rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .tag:hover, .tag.active {
            border-color: var(--amber);
            color: var(--amber);
        }

        .tag-team { color: var(--amber); border-color: var(--amber); opacity: 0.7; }
        .tag-team:hover { opacity: 1; }

        /* Empty state */
        .empty {
            text-align: center;
            padding: 5rem 0;
            color: var(--text-muted);
        }

        .empty p {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-style: italic;
        }

        .empty a { color: var(--amber); text-decoration: none; }
        .empty a:hover { text-decoration: underline; }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 0.875rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            border: 1px solid var(--border);
            text-decoration: none;
            transition: all 0.2s;
        }

        .pagination a:hover { border-color: var(--amber); color: var(--amber); }
        .pagination .active span { border-color: var(--amber); color: var(--amber); }

        /* Report */
        .report-btn {
            background: none;
            border: none;
            color: var(--text-dim);
            font-size: 0.72rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            padding: 0;
            margin-top: 0.75rem;
            display: block;
            transition: color 0.2s;
        }

        .report-btn:hover { color: #e87070; }

        /* Filter sidebar */
        .page-layout {
            display: grid;
            grid-template-columns: 1fr 200px;
            gap: 4rem;
            align-items: start;
        }

        .filters h3 {
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-dim);
            margin-bottom: 1rem;
            margin-top: 1.5rem;
        }

        .filters h3:first-child { margin-top: 0; }

        .filter-link {
            display: block;
            font-size: 0.82rem;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.2rem 0;
            transition: color 0.2s;
        }

        .filter-link:hover, .filter-link.active { color: var(--amber); }

        @media (max-width: 680px) {
            .page-layout { grid-template-columns: 1fr; }
            .filters { display: none; }
            nav { padding: 1rem; }
            .container { padding: 2rem 1rem; }
        }
    </style>
</head>
<body>

<nav>
    <a href="/" class="logo">Hardwood</a>
    <a href="/post" class="nav-link">Share a memory</a>
</nav>

<div class="container">
    @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>

</body>
</html>
