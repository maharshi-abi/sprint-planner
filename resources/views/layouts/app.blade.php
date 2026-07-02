<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sprint Tracker') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <script>
        // Default to dark mode unless explicitly set to light
        if (localStorage.getItem('color-theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
    </script>
    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }
        * { font-family: 'Outfit', sans-serif; }
        body { @apply text-slate-900 dark:text-slate-50; }
        h1, h2, h3, h4, h5, h6 { letter-spacing: -0.015em; font-weight: 600; }
        @layer base {
            .dark input:not([type="checkbox"]):not([type="radio"]),
            .dark select,
            .dark textarea,
            .dark option {
                @apply bg-slate-900/80 border-slate-700/60 text-slate-100;
            }
            .dark input[type="checkbox"],
            .dark input[type="radio"] {
                @apply bg-slate-800 border-slate-700;
            }
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { @apply bg-transparent; }
        ::-webkit-scrollbar-thumb { @apply bg-slate-300 dark:bg-slate-600/80 rounded-full border-2 border-solid border-transparent bg-clip-padding; }
        ::-webkit-scrollbar-thumb:hover { @apply bg-slate-400 dark:bg-slate-500 border-2 border-solid border-transparent bg-clip-padding; }
        
        /* Pro-level iOS Glass styles */
        .premium-card { @apply transition-all duration-300 hover:-translate-y-1 hover:shadow-xl; }
        .pro-panel { @apply rounded-3xl border shadow-sm transition-all duration-300 hover:shadow-md; }
        
        .nav-link { @apply flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-all duration-150; }
        .nav-link-active { @apply bg-slate-200/60 text-indigo-700 dark:bg-white/15 dark:text-white shadow-sm; }
        .nav-link-idle { @apply text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-indigo-100 dark:hover:bg-white/10 dark:hover:text-white; }
    </style>
    <style>
        /* ── iOS Glass Animated background ── */
        body { background: transparent !important; }
        #app-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: linear-gradient(135deg, #e4e7eb, #f2f5f9, #dfe5f0, #eaeef5);
            background-size: 400% 400%;
            animation: bgShift 30s ease infinite;
        }
        .dark #app-bg {
            background: linear-gradient(135deg, #090a10, #131221, #0c1524, #080c16);
        }
        @keyframes bgShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Grid overlay - faint in light mode, standard in dark */
        #app-grid {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image:
                linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px);
            background-size: 56px 56px;
            pointer-events: none;
        }
        .dark #app-grid {
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
        }

        /* Floating orbs */
        .app-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: -1;
        }
        .app-orb-1 {
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(236,72,153,0.15), transparent 70%);
            top: -220px; left: -200px;
            animation: orbFloat1 20s ease-in-out infinite;
        }
        .app-orb-2 {
            width: 550px; height: 550px;
            background: radial-gradient(circle, rgba(59,130,246,0.15), transparent 70%);
            bottom: -180px; right: -150px;
            animation: orbFloat2 26s ease-in-out infinite;
        }
        .app-orb-3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.15), transparent 70%);
            top: 40%; left: 55%;
            animation: orbFloat3 32s ease-in-out infinite;
        }
        .dark .app-orb-1 { background: radial-gradient(circle, rgba(79,70,229,0.15), transparent 70%); }
        .dark .app-orb-2 { background: radial-gradient(circle, rgba(109,40,217,0.12), transparent 70%); }
        .dark .app-orb-3 { background: radial-gradient(circle, rgba(6,182,212,0.07), transparent 70%); }

        @keyframes orbFloat1 {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(40px,-30px) scale(1.05); }
            66%      { transform: translate(-20px,20px) scale(0.97); }
        }
        @keyframes orbFloat2 {
            0%,100% { transform: translate(0,0) scale(1); }
            40%      { transform: translate(-30px,25px) scale(1.08); }
            70%      { transform: translate(20px,-15px) scale(0.95); }
        }
        @keyframes orbFloat3 {
            0%,100% { transform: translate(0,0); }
            50%      { transform: translate(-40px,-30px); }
        }

        /* ── iOS Glassmorphism - Light Mode ── */
        .glass-card,
        .bg-white,
        .pro-panel {
            background: rgba(255, 255, 255, 0.55) !important;
            backdrop-filter: blur(24px) saturate(180%);
            border-color: rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
            border-radius: 1.5rem; /* rounded-3xl equivalent */
        }
        #main-nav {
            background: rgba(255, 255, 255, 0.65) !important;
            backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }
        .nav-dropdown {
            background: rgba(255, 255, 255, 0.75) !important;
            backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            border-radius: 1rem;
        }

        /* ── iOS Glassmorphism - Dark Mode ── */
        .dark .glass-card,
        .dark .bg-white,
        .dark .pro-panel,
        .dark .dark\:bg-slate-800 {
            background: rgba(8,14,30,0.45) !important;
            backdrop-filter: blur(24px) saturate(120%);
            border-color: rgba(255,255,255,0.08) !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.40), inset 0 1px 0 rgba(255,255,255,0.06) !important;
        }
        .dark #main-nav {
            background: rgba(15,23,42,0.60) !important;
            backdrop-filter: blur(24px) saturate(150%);
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            box-shadow: 0 1px 32px rgba(0,0,0,0.40);
        }
        .dark .nav-dropdown {
            background: rgba(15,23,42,0.65) !important;
            backdrop-filter: blur(24px) saturate(150%);
            border: 1px solid rgba(255,255,255,0.08) !important;
        }
        /* Table header rows in dark mode */
        .dark .dark\:bg-slate-900,
        .dark .dark\:bg-slate-900\/50,
        .dark .dark\:bg-slate-900\/80 {
            background: rgba(8,12,30,0.45) !important;
        }
        /* Hover rows */
        .dark tr:hover td,
        .dark .dark\:hover\:bg-slate-700\/30:hover {
            background: rgba(99,102,241,0.06) !important;
        }
        /* Mobile nav in dark */
        .dark .dark\:bg-slate-800 .px-4.pt-3 {
            background: rgba(15,23,42,0.90) !important;
        }
        /* Page-enter animation */
        #page-content {
            animation: pageIn 0.45s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes pageIn {
            from { opacity:0; transform: translateY(16px); }
            to   { opacity:1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-transparent text-slate-900 dark:text-slate-100 min-h-screen antialiased selection:bg-indigo-200 selection:text-indigo-900 dark:selection:bg-indigo-500/30 dark:selection:text-indigo-100">
    <!-- Animated dark-mode background layers -->
    <div id="app-bg" aria-hidden="true"></div>
    <div id="app-grid" aria-hidden="true"></div>
    <div class="app-orb app-orb-1" aria-hidden="true"></div>
    <div class="app-orb app-orb-2" aria-hidden="true"></div>
    <div class="app-orb app-orb-3" aria-hidden="true"></div>
    @auth
    <nav id="main-nav" class="backdrop-blur-xl sticky top-0 z-50 text-slate-800 dark:text-white shadow-md border-b dark:border-slate-700 transition-colors duration-300"
         x-data="{ mobileMenuOpen: false, activeGroup: null }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">

                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-bold text-base tracking-tight shrink-0">
                    <div class="bg-indigo-100 dark:bg-white/20 rounded-lg p-1.5">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-700 to-indigo-500 dark:from-white dark:to-slate-300">
                        {{ config('app.name') }}
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-1">

                    {{-- Dashboard (main) --}}
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link-idle' }}">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    {{-- Separator --}}
                    <div class="w-px h-5 bg-slate-300 dark:bg-white/20 mx-1"></div>

                    {{-- Timer --}}
                    <a href="{{ route('timer.index') }}"
                       class="nav-link {{ request()->routeIs('timer.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Timer
                    </a>

                    {{-- Sprints --}}
                    <a href="{{ route('sprints.index') }}"
                       class="nav-link {{ request()->routeIs('sprints.*', 'tasks.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Sprints
                    </a>

                    {{-- Work Logs --}}
                    <a href="{{ route('work-sessions.index') }}"
                       class="nav-link {{ request()->routeIs('work-sessions.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Work Logs
                    </a>

                    {{-- Separator --}}
                    <div class="w-px h-5 bg-slate-300 dark:bg-white/20 mx-1"></div>

                    {{-- Analytics Dropdown --}}
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = !open"
                            class="nav-link {{ request()->routeIs('reports.*', 'dashboard.daily', 'dashboard.sprint') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Analytics
                            <svg class="w-3.5 h-3.5 opacity-70 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             class="nav-dropdown absolute left-0 mt-1.5 w-52 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50 py-1.5">
                            <p class="px-4 pt-1 pb-0.5 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Dashboards</p>
                            <a href="{{ route('dashboard.daily') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-700 {{ request()->routeIs('dashboard.daily') ? 'bg-indigo-50 dark:bg-slate-700 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Daily Overview
                            </a>
                            <a href="{{ route('dashboard.sprint') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-700 {{ request()->routeIs('dashboard.sprint') ? 'bg-indigo-50 dark:bg-slate-700 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                                Sprint Progress
                            </a>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1.5"></div>
                            <p class="px-4 pt-1 pb-0.5 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Reports</p>
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-700 {{ request()->routeIs('reports.index') ? 'bg-indigo-50 dark:bg-slate-700 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Weekly Report
                            </a>
                            <a href="{{ route('reports.monthly') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-700 {{ request()->routeIs('reports.monthly') ? 'bg-indigo-50 dark:bg-slate-700 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Monthly Analytics
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right: theme + user --}}
                <div class="hidden md:flex items-center gap-3">
                    <button type="button" id="theme-toggle"
                        class="text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-white p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-white/30 transition-colors">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    </button>
                    <div class="w-px h-5 bg-slate-300 dark:bg-white/20"></div>
                    <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-2.5">
                        @csrf
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 dark:bg-slate-600 flex items-center justify-center dark:text-white text-xs font-bold uppercase">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-slate-800 dark:text-white text-xs font-semibold leading-none">{{ auth()->user()->name }}</span>
                                <button type="submit" class="text-[11px] text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-slate-200 transition-colors mt-0.5 text-left">Sign out</button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden text-slate-500 hover:text-indigo-600 dark:text-indigo-200 dark:hover:text-white focus:outline-none p-2 rounded-lg">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden bg-white/90 dark:bg-slate-800 backdrop-blur-xl border-t border-slate-200 dark:border-slate-700 shadow-xl">
            <div class="px-4 pt-3 pb-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('timer.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('timer.*') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Timer
                </a>
                <a href="{{ route('sprints.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('sprints.*') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Sprints
                </a>
                <a href="{{ route('work-sessions.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('work-sessions.*') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Work Logs
                </a>
                <div class="border-t border-slate-200 dark:border-slate-700 pt-2 mt-2">
                    <p class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Analytics</p>
                    <a href="{{ route('dashboard.daily') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard.daily') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">Daily Overview</a>
                    <a href="{{ route('dashboard.sprint') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard.sprint') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">Sprint Progress</a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.index') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">Weekly Report</a>
                    <a href="{{ route('reports.monthly') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.monthly') ? 'bg-slate-100 text-indigo-700 dark:bg-white/15 dark:text-white' : 'text-slate-600 hover:bg-slate-50 dark:text-indigo-100 dark:hover:bg-white/10' }}">Monthly Analytics</a>
                </div>
                <div class="border-t border-slate-200 dark:border-slate-700 pt-3 mt-2 flex items-center justify-between px-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 dark:bg-slate-600 flex items-center justify-center dark:text-white text-xs font-bold uppercase">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <span class="text-slate-800 dark:text-white text-sm font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:text-indigo-600 dark:text-indigo-200 dark:hover:text-white text-sm font-medium">Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main id="page-content" class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-4 py-3">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
    <script>
        var themeToggleDarkIcon  = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        if (localStorage.getItem('color-theme') === 'light') {
            themeToggleDarkIcon?.classList.remove('hidden');
        } else {
            themeToggleLightIcon?.classList.remove('hidden');
        }
        var themeToggleBtn = document.getElementById('theme-toggle');
        themeToggleBtn?.addEventListener('click', function() {
            themeToggleDarkIcon?.classList.toggle('hidden');
            themeToggleLightIcon?.classList.toggle('hidden');
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        });
    </script>
</body>
</html>
