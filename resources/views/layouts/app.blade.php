<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardwood — Basketball Memories</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0a0908;
            --surface: #141210;
            --surface2: #1c1916;
            --border: #252018;
            --border2: #2e2820;
            --amber: #c8872a;
            --amber-light: #e8a84a;
            --amber-dim: rgba(200,135,42,0.12);
            --text: #ede5d8;
            --text-muted: #7a7060;
            --text-dim: #3a342a;
            --glow: rgba(200,135,42,0.15);
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-weight: 300;
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Grain texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1000;
            opacity: 0.4;
        }

        /* Ambient glow */
        body::after {
            content: '';
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(200,135,42,0.06) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Nav */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 900px;
            margin: 0 auto;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        nav::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 2rem;
            right: 2rem;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border2), transparent);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--amber);
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: opacity 0.2s;
        }

        .logo:hover { opacity: 0.8; }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            transition: all 0.25s;
            position: relative;
        }

        .nav-link:hover {
            color: var(--amber);
            border-color: var(--border2);
            background: var(--amber-dim);
        }

        /* Layout */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
            position: relative;
            z-index: 1;
        }

        /* Flash */
        .flash {
            background: var(--amber-dim);
            border: 1px solid var(--amber);
            border-left: 3px solid var(--amber);
            color: var(--amber-light);
            padding: 0.875rem 1.25rem;
            margin-bottom: 2rem;
            font-size: 0.88rem;
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.06) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }

        .btn:hover::before { transform: translateX(100%); }

        .btn-primary {
            background: var(--amber);
            color: #0a0908;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--amber-light);
            box-shadow: 0 0 20px rgba(200,135,42,0.3);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border2);
        }

        .btn-ghost:hover {
            border-color: var(--amber);
            color: var(--amber);
            background: var(--amber-dim);
        }

        /* Forms */
        .field-label {
            display: block;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.6rem;
            font-weight: 500;
        }

        textarea, input[type="text"] {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border2);
            color: var(--text);
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            padding: 1rem 1.25rem;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            resize: none;
        }

        textarea:focus, input[type="text"]:focus {
            border-color: var(--amber);
            background: var(--surface2);
            box-shadow: 0 0 0 3px rgba(200,135,42,0.08), inset 0 1px 3px rgba(0,0,0,0.3);
        }

        textarea::placeholder { color: var(--text-dim); font-style: italic; }

        .field-error {
            color: #e87070;
            font-size: 0.78rem;
            margin-top: 0.4rem;
        }

        /* Memory card */
        .memory-card {
            padding: 2rem 0;
            border-bottom: 1px solid var(--border);
            opacity: 0;
            transform: translateY(20px);
            transition: border-color 0.3s;
        }

        .memory-card:last-child { border-bottom: none; }
        .memory-card:hover { border-color: var(--border2); }

        .memory-body {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            line-height: 1.85;
            color: var(--text);
            margin-bottom: 1.25rem;
        }

        .memory-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 0.75rem;
        }

        .tag {
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            border: 1px solid var(--border2);
            padding: 0.2rem 0.55rem;
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }

        .tag:hover, .tag.active {
            border-color: var(--amber);
            color: var(--amber);
            background: var(--amber-dim);
        }

        .tag-team {
            color: rgba(200,135,42,0.7);
            border-color: rgba(200,135,42,0.25);
        }

        .tag-team:hover {
            color: var(--amber);
            border-color: var(--amber);
            background: var(--amber-dim);
        }

        /* Report button */
        .report-btn {
            background: none;
            border: none;
            color: var(--text-dim);
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            padding: 0;
            transition: color 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .report-btn:hover { color: #e87070; }

        /* Empty state */
        .empty {
            text-align: center;
            padding: 6rem 0;
            color: var(--text-muted);
        }

        .empty p {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-style: italic;
            margin-bottom: 2rem;
        }

        .empty a { color: var(--amber); text-decoration: none; }
        .empty a:hover { text-decoration: underline; }

        /* Page layout */
        .page-layout {
            display: grid;
            grid-template-columns: 1fr 210px;
            gap: 5rem;
            align-items: start;
        }

        /* Glassmorphism filter sidebar */
        .filters {
            position: sticky;
            top: 5rem;
            background: rgba(20,18,16,0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border2);
            padding: 1.5rem;
            opacity: 0;
        }

        .filters h3 {
            font-size: 0.65rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-dim);
            margin-bottom: 0.75rem;
            margin-top: 1.25rem;
            font-weight: 600;
        }

        .filters h3:first-child { margin-top: 0; }

        .filter-link {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.25rem 0;
            transition: color 0.2s, padding-left 0.2s;
        }

        .filter-link:hover, .filter-link.active {
            color: var(--amber);
            padding-left: 0.4rem;
        }

        .filter-clear {
            display: block;
            margin-top: 1.25rem;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--amber);
            text-decoration: none;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .filter-clear:hover { opacity: 1; }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 4rem;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 0.875rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            border: 1px solid var(--border);
            text-decoration: none;
            transition: all 0.2s;
        }

        .pagination a:hover { border-color: var(--amber); color: var(--amber); background: var(--amber-dim); }
        .pagination .active span { border-color: var(--amber); color: var(--amber); }

        /* Page header */
        .page-header {
            margin-bottom: 3rem;
            opacity: 0;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 400;
            color: var(--amber);
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Divider */
        .divider {
            width: 40px;
            height: 1px;
            background: var(--amber);
            margin: 1rem 0 2rem;
            opacity: 0.4;
        }

        @media (max-width: 700px) {
            .page-layout { grid-template-columns: 1fr; }
            .filters { display: none; }
            nav { padding: 1rem; }
            .container { padding: 2rem 1rem; }
            .page-header h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<nav>
    <a href="/" class="logo">Hardwood</a>
    <a href="/post" class="nav-link">+ Share a memory</a>
</nav>

<div class="container">
    @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>

<script>
    gsap.registerPlugin(ScrollTrigger);

    // Animate memory cards on scroll
    document.addEventListener('DOMContentLoaded', () => {
        // Animate filters in
        gsap.to('.filters', {
            opacity: 1,
            x: 0,
            duration: 0.6,
            ease: 'power2.out',
            delay: 0.3
        });

        // Animate page header
        gsap.to('.page-header', {
            opacity: 1,
            y: 0,
            duration: 0.5,
            ease: 'power2.out'
        });

        // Animate memory cards staggered on scroll
        gsap.utils.toArray('.memory-card').forEach((card, i) => {
            gsap.to(card, {
                opacity: 1,
                y: 0,
                duration: 0.5,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: card,
                    start: 'top 90%',
                    toggleActions: 'play none none none'
                },
                delay: i < 3 ? i * 0.08 : 0
            });
        });
    });
</script>

</body>
</html>
