<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rafters — Basketball Memories</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #080706;
            --surface: #111009;
            --surface2: #191612;
            --border: #1e1b14;
            --border2: #28231a;
            --amber-rgb: 200,135,42;
            --amber: #c8872a;
            --amber-light: #e8a84a;
            --amber-dim: rgba(var(--amber-rgb),0.1);
            --amber-glow: rgba(var(--amber-rgb),0.2);
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
            border: 1px solid rgba(var(--amber-rgb),0.4);
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

        /* Ambient background — soft glows in the current team color, drifting
           slowly behind everything. Fixed so it reads as atmosphere rather
           than scrolling content. */
        body::after {
            content: '';
            position: fixed;
            inset: -10%;
            z-index: -1;
            pointer-events: none;
            background:
                radial-gradient(ellipse 700px 550px at 12% 15%, rgba(var(--amber-rgb),0.10), transparent 60%),
                radial-gradient(ellipse 800px 650px at 88% 55%, rgba(var(--amber-rgb),0.07), transparent 62%),
                radial-gradient(ellipse 600px 550px at 45% 95%, rgba(var(--amber-rgb),0.06), transparent 60%),
                radial-gradient(ellipse 500px 500px at 75% 10%, rgba(var(--amber-rgb),0.05), transparent 55%);
            animation: ambientDrift 40s ease-in-out infinite alternate;
        }

        @keyframes ambientDrift {
            0%   { transform: translate(0%, 0%) scale(1); }
            50%  { transform: translate(-1.5%, 1%) scale(1.05); }
            100% { transform: translate(1.5%, -1%) scale(1.02); }
        }

        @media (prefers-reduced-motion: reduce) {
            body::after { animation: none; }
        }

        /* Nav */
        .site-nav {
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

        .site-nav::after {
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

        .admin-toggle-form { margin: 0; }

        .admin-toggle {
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .admin-toggle-on {
            color: var(--amber-light) !important;
        }

        .team-picker {
            background: var(--surface);
            border: 1px solid var(--border2);
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
            font-size: 0.68rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0.45rem 0.5rem;
            border-radius: 3px;
            cursor: pointer;
            max-width: 130px;
        }

        .team-picker:hover, .team-picker:focus {
            border-color: var(--amber);
            color: var(--text);
            outline: none;
        }

        /* Mix tapes */
        .mixtape-launcher {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 700;
            display: none;
            align-items: center;
            gap: 0.5rem;
            background: var(--surface2);
            border: 1px solid var(--amber);
            color: var(--amber-light);
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            padding: 0.75rem 1.25rem;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5), 0 0 20px rgba(var(--amber-rgb),0.15);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .mixtape-launcher:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 34px rgba(0,0,0,0.55), 0 0 26px rgba(var(--amber-rgb),0.25);
        }

        .mixtape-overlay {
            position: fixed;
            inset: 0;
            z-index: 900;
            background: rgba(6,5,4,0.97);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .mixtape-builder {
            position: relative;
            width: 100%;
            max-width: 480px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-top: 2px solid var(--amber);
            padding: 2rem;
        }

        .mixtape-close {
            position: absolute;
            top: 1.25rem;
            right: 1.5rem;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .mixtape-close:hover { color: var(--amber); }

        .mixtape-heading {
            font-family: 'Playfair Display', serif;
            font-weight: 400;
            font-size: 1.4rem;
            color: var(--amber);
            margin-bottom: 1.25rem;
        }

        .mixtape-title-input {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 4px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            padding: 0.65rem 0.85rem;
            margin-bottom: 1.25rem;
        }

        .mixtape-title-input:focus { outline: none; border-color: var(--amber); }

        .mixtape-tracklist {
            list-style: none;
            max-height: 220px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
        }

        .mixtape-track {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border);
        }

        .mixtape-track-num {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-style: italic;
            color: var(--text-dim);
            font-size: 0.8rem;
            width: 1.4rem;
            flex-shrink: 0;
        }

        .mixtape-track-excerpt {
            flex: 1;
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .mixtape-track-controls {
            display: flex;
            gap: 0.35rem;
            flex-shrink: 0;
        }

        .mixtape-track-controls button {
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 3px;
            color: var(--text-muted);
            font-size: 0.68rem;
            width: 22px;
            height: 22px;
            cursor: pointer;
        }

        .mixtape-track-controls button:hover:not(:disabled) { color: var(--amber); border-color: var(--amber); }
        .mixtape-track-controls button:disabled { opacity: 0.3; cursor: default; }

        .mixtape-error {
            color: #e87070;
            font-size: 0.78rem;
            margin-bottom: 1rem;
        }

        .mixtape-submit {
            width: 100%;
            text-align: center;
            border: none;
            cursor: pointer;
        }

        .mixtape-archive {
            border-top: 1px solid var(--border2);
            padding-top: 1.25rem;
            margin-top: 1.5rem;
        }

        .mixtape-archive-heading {
            font-family: 'Playfair Display', serif;
            font-weight: 400;
            font-size: 0.95rem;
            color: var(--amber);
            margin-bottom: 0.35rem;
        }

        .mixtape-archive-hint {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-bottom: 0.85rem;
        }

        .mixtape-archive-list {
            max-height: 160px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .mixtape-archive-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            line-height: 1.5;
        }

        .mixtape-archive-item span {
            flex: 1;
        }

        .mixtape-archive-item button {
            flex-shrink: 0;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 3px;
            color: var(--text-muted);
            font-size: 0.62rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0.35rem 0.6rem;
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
        }

        .mixtape-archive-item button:hover { color: var(--amber); border-color: var(--amber); }
        .mixtape-archive-item button.active { color: var(--amber-light); border-color: var(--amber); }

        .mixtape-archive-submit summary {
            font-size: 0.72rem;
            color: var(--text-muted);
            cursor: pointer;
            margin-bottom: 0.75rem;
        }

        .mixtape-archive-submit summary:hover { color: var(--amber); }

        .mixtape-dunk-input {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 4px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.8rem;
            padding: 0.6rem 0.75rem;
            min-height: 70px;
            resize: vertical;
            margin-bottom: 0.6rem;
        }

        .mixtape-dunk-input:focus { outline: none; border-color: var(--amber); }

        .mixtape-dunk-submit {
            width: 100%;
            text-align: center;
            font-size: 0.68rem;
            padding: 0.6rem;
            cursor: pointer;
        }

        .mixtape-dunk-status {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 0.6rem;
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

        .game-media {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem 1.25rem;
            margin-top: 0.85rem;
        }

        .game-media-item {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        a.game-media-item:hover { color: var(--amber); }
        .game-media-item a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .game-media-item a:hover { color: var(--amber); }

        .game-mates-link {
            display: inline-block;
            margin-top: 0.6rem;
            font-size: 0.75rem;
            color: var(--amber);
            text-decoration: none;
            border-bottom: 1px dotted rgba(var(--amber-rgb),0.4);
            transition: border-color 0.2s;
        }

        .game-mates-link:hover { border-color: var(--amber); }

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
            color: rgba(var(--amber-rgb),0.6);
            border-color: rgba(var(--amber-rgb),0.2);
        }

        .tag-team:hover { color: var(--amber); border-color: var(--amber); background: var(--amber-dim); }

        .tag-venue {
            color: var(--text-muted);
            border-color: var(--border2);
        }

        .tag-venue:hover { color: var(--amber); border-color: var(--amber); background: var(--amber-dim); }

        .tag-near-me {
            background: none;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
        }

        .tag-near-me:hover { color: var(--amber); border-color: var(--amber); background: var(--amber-dim); }
        .tag-near-me.loading { opacity: 0.5; cursor: default; }

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
        .pagination span.active { border-color: var(--amber); color: var(--amber); }
        .pagination span.disabled { opacity: 0.3; }

        @media (max-width: 700px) {
            .site-nav { padding: 1.25rem 1.5rem; }
            #cursor, #cursor-ring { display: none; }
            body { cursor: auto; }
            .btn { cursor: pointer; }
            .report-btn { cursor: pointer; }
        }
    </style>
    <script>
        // Team theming — picks a random NBA team's color as the site accent
        // on every load, unless one is pinned via the picker in the nav.
        // Runs synchronously before <body> paints to avoid a flash of the
        // default color. Only sets --amber/--amber-light/--amber-rgb; every
        // other color in the site (glows, dims, borders) is derived from
        // those via CSS so this is the only place team color logic lives.
        (function () {
            window.RAFTERS_TEAMS = [
                { name: 'Atlanta Hawks', color: '#E13A3E' },
                { name: 'Boston Celtics', color: '#008348' },
                { name: 'Brooklyn Nets', color: '#F2F2F2' },
                { name: 'Charlotte Hornets', color: '#00A8B0' },
                { name: 'Chicago Bulls', color: '#CE1141' },
                { name: 'Cleveland Cavaliers', color: '#FDBB30' },
                { name: 'Dallas Mavericks', color: '#0064B1' },
                { name: 'Denver Nuggets', color: '#FEC524' },
                { name: 'Detroit Pistons', color: '#ED174C' },
                { name: 'Golden State Warriors', color: '#FDB927' },
                { name: 'Houston Rockets', color: '#F9423A' },
                { name: 'Indiana Pacers', color: '#FFC633' },
                { name: 'LA Clippers', color: '#E0115F' },
                { name: 'LA Lakers', color: '#6F2DA8' },
                { name: 'Memphis Grizzlies', color: '#5D76A9' },
                { name: 'Miami Heat', color: '#F9A01B' },
                { name: 'Milwaukee Bucks', color: '#00A94F' },
                { name: 'Minnesota Timberwolves', color: '#78BE20' },
                { name: 'New Orleans Pelicans', color: '#E31837' },
                { name: 'New York Knicks', color: '#F58426' },
                { name: 'Oklahoma City Thunder', color: '#EF3B24' },
                { name: 'Orlando Magic', color: '#0077C0' },
                { name: 'Philadelphia 76ers', color: '#006BB6' },
                { name: 'Phoenix Suns', color: '#E56020' },
                { name: 'Portland Trail Blazers', color: '#D62828' },
                { name: 'Sacramento Kings', color: '#5A2D81' },
                { name: 'San Antonio Spurs', color: '#C4CED4' },
                { name: 'Toronto Raptors', color: '#753BBD' },
                { name: 'Utah Jazz', color: '#63C7B2' },
                { name: 'Washington Wizards', color: '#DC143C' },
            ];

            const STORE_KEY = 'rafters-team';
            const pinned = localStorage.getItem(STORE_KEY);
            const team = window.RAFTERS_TEAMS.find((t) => t.name === pinned)
                || window.RAFTERS_TEAMS[Math.floor(Math.random() * window.RAFTERS_TEAMS.length)];

            const n = parseInt(team.color.slice(1), 16);
            const r = (n >> 16) & 255, g = (n >> 8) & 255, b = n & 255;
            const light = [r, g, b].map((c) => Math.min(255, Math.round(c + (255 - c) * 0.28)));

            const root = document.documentElement.style;
            root.setProperty('--amber-rgb', `${r},${g},${b}`);
            root.setProperty('--amber', team.color);
            root.setProperty('--amber-light', `rgb(${light[0]},${light[1]},${light[2]})`);

            window.RAFTERS_CURRENT_TEAM = team.name;
            window.RAFTERS_PINNED_TEAM = pinned;
        })();
    </script>
</head>
<body>

{{-- Custom cursor --}}
<div id="cursor"></div>
<div id="cursor-ring"></div>

<nav class="site-nav">
    <a href="/" class="logo">Rafters</a>
    <div class="nav-right">
        <a href="/" class="nav-link">Feed</a>
        <select id="team-theme-picker" class="team-picker" title="Team theme"></select>
        <a href="/post" class="nav-cta">Share a memory</a>
        @auth
            <form action="{{ route('admin.exit') }}" method="POST" class="admin-toggle-form">
                @csrf
                <button type="submit" class="nav-link admin-toggle admin-toggle-on">Admin mode: on</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="nav-link admin-toggle">Admin</a>
        @endauth
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

    // Team theme picker — lets you pin a specific team instead of getting
    // a random one every reload. Colors themselves are already applied by
    // the inline script in <head>; this just builds/wires the <select>.
    document.addEventListener('DOMContentLoaded', () => {
        const picker = document.getElementById('team-theme-picker');
        if (picker) {
            const randomOption = document.createElement('option');
            randomOption.value = '';
            randomOption.textContent = '🎲 Random';
            picker.appendChild(randomOption);

            window.RAFTERS_TEAMS.forEach((team) => {
                const option = document.createElement('option');
                option.value = team.name;
                option.textContent = team.name;
                if (team.name === window.RAFTERS_PINNED_TEAM) {
                    option.selected = true;
                }
                picker.appendChild(option);
            });

            picker.addEventListener('change', () => {
                if (picker.value) {
                    localStorage.setItem('rafters-team', picker.value);
                } else {
                    localStorage.removeItem('rafters-team');
                }
                location.reload();
            });
        }
    });

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

        // ScrollTrigger calculates each card's trigger position from the
        // page's layout at the moment this runs. Playfair Display/Inter
        // are web fonts that can still be downloading at that point —
        // when they swap in, every card's text reflows and every trigger
        // position below it goes stale, leaving cards stuck mid-fade
        // (partial opacity, offset) instead of settling in. With a full
        // feed of cards this compounds into what looks like broken,
        // overlapping content. Recalculating once fonts are actually
        // ready fixes the positions for good.
        document.fonts.ready.then(() => ScrollTrigger.refresh());
    });

    // Card tilt — subtle 3D parallax that follows the cursor, skipped on
    // touch devices and when a selection is being dragged (so it doesn't
    // fight annotation text-selection) or reduced-motion is requested.
    document.addEventListener('DOMContentLoaded', () => {
        const canTilt = window.matchMedia('(hover: hover)').matches
            && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!canTilt) return;

        document.querySelectorAll('.memory-card').forEach((card) => {
            card.addEventListener('mousemove', (e) => {
                if (e.buttons !== 0) return;

                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;

                gsap.to(card, {
                    rotateX: -y * 6,
                    rotateY: x * 6,
                    transformPerspective: 900,
                    duration: 0.4,
                    ease: 'power2.out',
                    overwrite: 'auto',
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    rotateX: 0,
                    rotateY: 0,
                    duration: 0.6,
                    ease: 'power2.out',
                    overwrite: 'auto',
                });
            });
        });
    });
</script>

<script>
    // Mix tapes — build a personal collection of memories and dunk-archive
    // entries entirely client-side (no accounts, matching the rest of the
    // site), then "cut" it once to get a permanent shareable page. The
    // draft persists across pages via localStorage until it's submitted or
    // cleared. Tracks come from two sources: memory cards on the feed
    // (already in the DOM) and the dunk archive (fetched lazily from
    // /dunks the first time the builder opens).
    document.addEventListener('DOMContentLoaded', () => {
        const MIN_TRACKS = {{ \App\Models\Mixtape::MIN_TRACKS }};
        const MAX_TRACKS = {{ \App\Models\Mixtape::MAX_TRACKS }};
        const DRAFT_KEY = 'rafters-mixtape-draft';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const loadDraft = () => {
            try {
                return JSON.parse(localStorage.getItem(DRAFT_KEY) || '[]');
            } catch {
                return [];
            }
        };
        const saveDraft = () => localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));

        let draft = loadDraft(); // [{ type: 'memory'|'dunk', id, excerpt }]
        let archive = null; // lazily-loaded approved dunks
        let archiveLoading = false;

        const inDraft = (type, id) => draft.some((t) => t.type === type && t.id === id);

        const launcher = document.createElement('button');
        launcher.type = 'button';
        launcher.className = 'mixtape-launcher';
        document.body.appendChild(launcher);

        const updateLauncher = () => {
            launcher.textContent = `🎧 Mix Tape (${draft.length})`;
            launcher.style.display = draft.length > 0 ? 'flex' : 'none';
        };

        const syncAddButtons = () => {
            document.querySelectorAll('.mixtape-add-btn').forEach((btn) => {
                const active = inDraft(btn.dataset.type || 'memory', btn.dataset.trackId);
                btn.classList.toggle('active', active);
                btn.textContent = active ? '✓ added' : '+ tape';
            });
            document.querySelectorAll('.mixtape-archive-item button').forEach((btn) => {
                const active = inDraft('dunk', btn.dataset.trackId);
                btn.classList.toggle('active', active);
                btn.textContent = active ? '✓ added' : '+ add';
            });
        };

        const toggleTrack = (type, id, excerpt) => {
            const idx = draft.findIndex((t) => t.type === type && t.id === id);

            if (idx > -1) {
                draft.splice(idx, 1);
            } else {
                if (draft.length >= MAX_TRACKS) {
                    alert(`A tape only holds ${MAX_TRACKS} tracks. Remove one to add another.`);
                    return;
                }
                draft.push({ type, id, excerpt });
            }

            saveDraft();
            syncAddButtons();
            updateLauncher();
        };

        document.querySelectorAll('.mixtape-add-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                toggleTrack(btn.dataset.type || 'memory', btn.dataset.trackId, btn.dataset.excerpt);
            });
        });

        const overlay = document.createElement('div');
        overlay.className = 'mixtape-overlay';
        overlay.innerHTML = `
            <div class="mixtape-builder">
                <button type="button" class="mixtape-close" aria-label="Close">✕</button>
                <h2 class="mixtape-heading">Cut a Mix Tape</h2>
                <input type="text" class="mixtape-title-input" maxlength="80" placeholder="Give it a title...">
                <ol class="mixtape-tracklist"></ol>
                <p class="mixtape-error" style="display:none;"></p>
                <button type="button" class="btn btn-primary mixtape-submit">Cut this tape 🎙️</button>

                <div class="mixtape-archive">
                    <h3 class="mixtape-archive-heading">🏀 Dunk Archive</h3>
                    <p class="mixtape-archive-hint">Splice a legendary dunk into the tape.</p>
                    <div class="mixtape-archive-list"></div>
                    <details class="mixtape-archive-submit">
                        <summary>Add a dunk to the archive</summary>
                        <textarea class="mixtape-dunk-input" maxlength="500" placeholder="Describe the dunk..."></textarea>
                        <button type="button" class="btn btn-ghost mixtape-dunk-submit">Submit for review</button>
                        <p class="mixtape-dunk-status" style="display:none;"></p>
                    </details>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        const tracklistEl = overlay.querySelector('.mixtape-tracklist');
        const errorEl = overlay.querySelector('.mixtape-error');
        const titleInput = overlay.querySelector('.mixtape-title-input');
        const archiveListEl = overlay.querySelector('.mixtape-archive-list');
        const dunkInput = overlay.querySelector('.mixtape-dunk-input');
        const dunkStatusEl = overlay.querySelector('.mixtape-dunk-status');

        function renderArchive() {
            if (!archive) {
                archiveListEl.textContent = archiveLoading ? 'Loading…' : '';
                return;
            }

            archiveListEl.innerHTML = '';

            if (archive.length === 0) {
                archiveListEl.textContent = 'No dunks in the archive yet.';
                return;
            }

            archive.forEach((dunk) => {
                const row = document.createElement('div');
                row.className = 'mixtape-archive-item';

                const excerpt = document.createElement('span');
                excerpt.textContent = dunk.body;

                const add = document.createElement('button');
                add.type = 'button';
                add.dataset.trackId = dunk.id;
                const active = inDraft('dunk', dunk.id);
                add.classList.toggle('active', active);
                add.textContent = active ? '✓ added' : '+ add';
                add.addEventListener('click', () => toggleTrack('dunk', dunk.id, dunk.body.slice(0, 90)));

                row.append(excerpt, add);
                archiveListEl.appendChild(row);
            });
        }

        function loadArchive() {
            if (archive || archiveLoading) return;
            archiveLoading = true;
            renderArchive();

            fetch('/dunks', { headers: { Accept: 'application/json' } })
                .then((res) => res.json())
                .then((data) => {
                    archive = Array.isArray(data) ? data : [];
                })
                .catch(() => {
                    archive = [];
                })
                .finally(() => {
                    archiveLoading = false;
                    renderArchive();
                });
        }

        overlay.querySelector('.mixtape-dunk-submit').addEventListener('click', () => {
            const body = dunkInput.value.trim();
            dunkStatusEl.style.display = 'block';

            if (body.length < 10) {
                dunkStatusEl.textContent = 'Give it at least a few words.';
                return;
            }

            dunkStatusEl.textContent = 'Submitting…';

            fetch('/dunks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ body }),
            })
                .then((res) => res.json().then((data) => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok) {
                        dunkStatusEl.textContent = data.message || 'Something went wrong.';
                        return;
                    }

                    dunkInput.value = '';

                    if (data.status === 'approved') {
                        dunkStatusEl.textContent = 'Added to the archive.';
                        if (archive) {
                            archive.unshift(data);
                            renderArchive();
                        }
                    } else {
                        dunkStatusEl.textContent = "Submitted — it needs a quick review before it joins the archive.";
                    }
                })
                .catch(() => {
                    dunkStatusEl.textContent = 'Something went wrong.';
                });
        });

        function renderTracklist() {
            tracklistEl.innerHTML = '';

            draft.forEach((track, i) => {
                const li = document.createElement('li');
                li.className = 'mixtape-track';

                const num = document.createElement('span');
                num.className = 'mixtape-track-num';
                num.textContent = String(i + 1).padStart(2, '0');

                const excerpt = document.createElement('span');
                excerpt.className = 'mixtape-track-excerpt';
                excerpt.textContent = (track.type === 'dunk' ? '🏀 ' : '') + track.excerpt;

                const controls = document.createElement('span');
                controls.className = 'mixtape-track-controls';

                const up = document.createElement('button');
                up.type = 'button';
                up.textContent = '↑';
                up.disabled = i === 0;
                up.addEventListener('click', () => {
                    [draft[i - 1], draft[i]] = [draft[i], draft[i - 1]];
                    saveDraft();
                    renderTracklist();
                });

                const down = document.createElement('button');
                down.type = 'button';
                down.textContent = '↓';
                down.disabled = i === draft.length - 1;
                down.addEventListener('click', () => {
                    [draft[i + 1], draft[i]] = [draft[i], draft[i + 1]];
                    saveDraft();
                    renderTracklist();
                });

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '✕';
                remove.addEventListener('click', () => {
                    draft.splice(i, 1);
                    saveDraft();
                    renderTracklist();
                    syncAddButtons();
                    updateLauncher();
                });

                controls.append(up, down, remove);
                li.append(num, excerpt, controls);
                tracklistEl.appendChild(li);
            });
        }

        launcher.addEventListener('click', () => {
            errorEl.style.display = 'none';
            renderTracklist();
            loadArchive();
            overlay.style.display = 'flex';
        });

        overlay.querySelector('.mixtape-close').addEventListener('click', () => {
            overlay.style.display = 'none';
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.style.display = 'none';
        });

        overlay.querySelector('.mixtape-submit').addEventListener('click', () => {
            const title = titleInput.value.trim();

            if (title.length < 3) {
                errorEl.textContent = 'Give your tape a title (at least 3 characters).';
                errorEl.style.display = 'block';
                return;
            }

            if (draft.length < MIN_TRACKS) {
                errorEl.textContent = `Add at least ${MIN_TRACKS} tracks to cut a tape.`;
                errorEl.style.display = 'block';
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("mixtapes.store") }}';
            form.style.display = 'none';

            const fields = {
                _token: csrfToken,
                title,
                team_name: window.RAFTERS_CURRENT_TEAM || '',
                team_color: getComputedStyle(document.documentElement).getPropertyValue('--amber').trim(),
            };

            Object.entries(fields).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            });

            draft.forEach((track) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tracks[]';
                input.value = `${track.type}:${track.id}`;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            localStorage.removeItem(DRAFT_KEY);
            form.submit();
        });

        syncAddButtons();
        updateLauncher();
    });
</script>

@yield('scripts')

</body>
</html>
