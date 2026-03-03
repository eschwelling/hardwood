<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardwood — Basketball Memories</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/SplitText.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #080706;
            --surface: #111009;
            --surface2: #191612;
            --border: #1e1b14;
            --border2: #28231a;
            --amber: #c8872a;
            --amber-light: #e8a84a;
            --amber-dim: rgba(200,135,42,0.1);
            --amber-glow: rgba(200,135,42,0.2);
            --text: #f0e8d8;
            --text-muted: #6a6050;
            --text-dim: #2e2820;
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
            cursor: none;
        }

        /* Custom cursor */
        #cursor {
            position: fixed;
            width: 10px;
            height: 10px;
            background: var(--amber);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: transform 0.1s, width 0.3s, height 0.3s, background 0.3s;
            mix-blend-mode: screen;
        }

        #cursor-ring {
            position: fixed;
            width: 36px;
            height: 36px;
            border: 1px solid rgba(200,135,42,0.4);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9998;
            transform: translate(-50%, -50%);
            transition: transform 0.15s ease-out, width 0.3s, height 0.3s, opacity 0.3s;
        }

        body:has(a:hover) #cursor,
        body:has(button:hover) #cursor {
            width: 20px;
            height: 20px;
            background: var(--amber-light);
        }

        body:has(a:hover) #cursor-ring,
        body:has(button:hover) #cursor-ring {
            width: 50px;
            height: 50px;
            opacity: 0.6;
        }

        /* Grain */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1000;
            opacity: 0.5;
        }

        /* Nav */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 500;
            padding: 1.5rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            mix-blend-mode: normal;
        }

        nav::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(8,7,6,0.9) 0%, transparent 100%);
            pointer-events: none;
            z-index: -1;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--amber);
            text-decoration: none;
            letter-spacing: 0.06em;
            font-weight: 700;
            text-transform: uppercase;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover { color: var(--amber); }

        .nav-cta {
            color: var(--bg);
            background: var(--amber);
            text-decoration: none;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            transition: background 0.2s, box-shadow 0.2s;
        }

        .nav-cta:hover {
            background: var(--amber-light);
            box-shadow: 0 0 25px var(--amber-glow);
        }

        /* Main content */
        main {
            position: relative;
            z-index: 1;
        }

        /* Flash */
        .flash-wrap {
            position: fixed;
            top: 5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 600;
            width: 100%;
            max-width: 500px;
            padding: 0 2rem;
        }

        .flash {
            background: rgba(20,18,12,0.95);
            border: 1px solid var(--amber);
            border-left: 3px solid var(--amber);
            color: var(--amber-light);
            padding: 1rem 1.5rem;
            font-size: 0.88rem;
            backdrop-filter: blur(20px);
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-decoration: none;
            cursor: none;
            border: none;
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--amber);
            color: var(--bg);
        }

        .btn-primary:hover {
            background: var(--amber-light);
            box-shadow: 0 0 30px var(--amber-glow);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border2);
        }

        .btn-ghost:hover {
            border-color: var(--amber);
            color: var(--amber);
        }

        /* Forms */
        .field-label {
            display: block;
            font-size: 0.68rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        textarea {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 2px solid var(--border2);
            color: var(--text);
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            padding: 1rem 0;
            outline: none;
            transition: border-color 0.3s;
            resize: none;
            background: transparent;
        }

        textarea:focus {
            border-bottom-color: var(--amber);
        }

        textarea::placeholder { color: var(--text-dim); font-style: italic; }

        .field-error {
            color: #e87070;
            font-size: 0.78rem;
            margin-top: 0.5rem;
        }

        /* Memory card */
        .memory-card {
            padding: 2.5rem 0;
            border-bottom: 1px solid var(--border);
            position: relative;
            opacity: 0;
            transform: translateY(30px);
        }

        .memory-card::before {
            content: '';
            position: absolute;
            left: -3rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, transparent, var(--amber), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .memory-card:hover::before { opacity: 0.4; }

        .memory-body {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            line-height: 1.9;
            color: var(--text);
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }

        .memory-card:hover .memory-body { color: #fff; }

        .memory-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .tag {
            font-size: 0.65rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-muted);
            border: 1px solid var(--border2);
            padding: 0.2rem 0.6rem;
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
            color: rgba(200,135,42,0.6);
            border-color: rgba(200,135,42,0.2);
        }

        .tag-team:hover { color: var(--amber); border-color: var(--amber); background: var(--amber-dim); }

        .report-btn {
            background: none;
            border: none;
            color: var(--text-dim);
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: none;
            padding: 0;
            margin-top: 1rem;
            display: block;
            transition: color 0.2s;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
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
            font-size: 1.2rem;
            font-style: italic;
            margin-bottom: 2rem;
        }

        /* Horizontal tag filter */
        .tag-filter-bar {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 1.5rem 0;
            margin-bottom: 1rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
            border-bottom: 1px solid var(--border);
        }

        .tag-filter-bar::-webkit-scrollbar { display: none; }

        .tag-filter-group {
            display: flex;
            gap: 0.4rem;
            flex-shrink: 0;
        }

        .tag-filter-divider {
            width: 1px;
            background: var(--border2);
            margin: 0 0.5rem;
            flex-shrink: 0;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 5rem;
            padding-bottom: 5rem;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 0.875rem;
            font-size: 0.78rem;
            color: var(--text-muted);
            border: 1px solid var(--border);
            text-decoration: none;
            transition: all 0.2s;
        }

        .pagination a:hover { border-color: var(--amber); color: var(--amber); }
        .pagination .active span { border-color: var(--amber); color: var(--amber); }

        @media (max-width: 700px) {
            nav { padding: 1.25rem 1.5rem; }
            #cursor, #cursor-ring { display: none; }
            body { cursor: auto; }
            .btn { cursor: pointer; }
            .report-btn { cursor: pointer; }
        }
    </style>
</head>
<body>

{{-- Custom cursor --}}
<div id="cursor"></div>
<div id="cursor-ring"></div>

<nav>
    <a href="/" class="logo">Hardwood</a>
    <div class="nav-right">
        <a href="/" class="nav-link">Feed</a>
        <a href="/post" class="nav-cta">Share a memory</a>
    </div>
</nav>

@if(session('success'))
    <div class="flash-wrap">
        <div class="flash">{{ session('success') }}</div>
    </div>
@endif

<main>
    @yield('content')
</main>

<script>
    gsap.registerPlugin(ScrollTrigger);

    // Custom cursor
    const cursor = document.getElementById('cursor');
    const ring = document.getElementById('cursor-ring');
    let mouseX = 0, mouseY = 0;
    let ringX = 0, ringY = 0;

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        gsap.to(cursor, { x: mouseX, y: mouseY, duration: 0.1 });
    });

    // Ring follows with lag
    function animateRing() {
        ringX += (mouseX - ringX) * 0.12;
        ringY += (mouseY - ringY) * 0.12;
        gsap.set(ring, { x: ringX, y: ringY });
        requestAnimationFrame(animateRing);
    }
    animateRing();

    // Animate memory cards on scroll
    document.addEventListener('DOMContentLoaded', () => {
        gsap.utils.toArray('.memory-card').forEach((card, i) => {
            gsap.to(card, {
                opacity: 1,
                y: 0,
                duration: 0.6,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: card,
                    start: 'top 88%',
                },
                delay: i < 4 ? i * 0.07 : 0
            });
        });
    });
</script>

@yield('scripts')

</body>
</html>
