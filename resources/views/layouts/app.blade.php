<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sprint Tracker') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) || !('color-theme' in localStorage)) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }
        @layer base {
            .dark input:not([type="checkbox"]):not([type="radio"]),
            .dark select,
            .dark textarea,
            .dark option {
                @apply bg-slate-800 border-slate-700 text-slate-100;
            }
            .dark input[type="checkbox"],
            .dark input[type="radio"] {
                @apply bg-slate-800 border-slate-700;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            @apply bg-transparent;
        }
        ::-webkit-scrollbar-thumb {
            @apply bg-slate-300 dark:bg-slate-600 rounded-full border-2 border-solid border-transparent bg-clip-padding;
        }
        ::-webkit-scrollbar-thumb:hover {
            @apply bg-slate-400 dark:bg-slate-500 border-2 border-solid border-transparent bg-clip-padding;
        }
        
        /* Global Card Hover Enhancements */
        .premium-card {
            @apply transition-all duration-300 hover:-translate-y-1 hover:shadow-lg;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-200 antialiased selection:bg-indigo-200 selection:text-indigo-900 dark:selection:bg-indigo-500/30 dark:selection:text-indigo-100">
    @auth
    <nav class="bg-indigo-700/90 dark:bg-slate-800/80 backdrop-blur-md sticky top-0 z-50 text-white shadow-sm border-b border-indigo-600 dark:border-slate-700 transition-colors duration-200" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <a href="{{ route('dashboard.daily') }}" class="font-bold text-lg tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-indigo-200 dark:from-white dark:to-slate-300">{{ config('app.name') }}</a>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-1 text-sm font-medium">
                <!-- Dashboards -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-1 px-3 py-2 rounded hover:bg-indigo-600 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('dashboard.*') ? 'bg-indigo-800 dark:bg-slate-900' : '' }}">
                        Dashboards
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.opacity.duration.200ms class="absolute left-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden z-50 py-1">
                        <a href="{{ route('dashboard.daily') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ request()->routeIs('dashboard.daily') ? 'bg-indigo-50 dark:bg-slate-900/50 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">Daily Overview</a>
                        <a href="{{ route('dashboard.sprint') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ request()->routeIs('dashboard.sprint') ? 'bg-indigo-50 dark:bg-slate-900/50 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">Sprint Progress</a>
                    </div>
                </div>

                <!-- Timer -->
                <a href="{{ route('timer.index') }}" class="px-3 py-2 rounded hover:bg-indigo-600 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('timer.*') ? 'bg-indigo-800 dark:bg-slate-900' : '' }}">Timer</a>

                <!-- Sprints -->
                <a href="{{ route('sprints.index') }}" class="px-3 py-2 rounded hover:bg-indigo-600 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('sprints.*', 'tasks.*') ? 'bg-indigo-800 dark:bg-slate-900' : '' }}">Sprints</a>

                <!-- Reports -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-1 px-3 py-2 rounded hover:bg-indigo-600 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('reports.*', 'work-sessions.*') ? 'bg-indigo-800 dark:bg-slate-900' : '' }}">
                        Reports
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.opacity.duration.200ms class="absolute left-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden z-50 py-1">
                        <a href="{{ route('work-sessions.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ request()->routeIs('work-sessions.*') ? 'bg-indigo-50 dark:bg-slate-900/50 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">Work Logs</a>
                        <a href="{{ route('reports.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ request()->routeIs('reports.index') ? 'bg-indigo-50 dark:bg-slate-900/50 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">Weekly Summary</a>
                        <a href="{{ route('reports.monthly') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ request()->routeIs('reports.monthly') ? 'bg-indigo-50 dark:bg-slate-900/50 text-indigo-700 dark:text-indigo-400 font-semibold' : '' }}">Monthly Analytics</a>
                    </div>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="hidden md:flex items-center gap-4">
                <button type="button" id="theme-toggle" class="text-indigo-200 dark:text-slate-300 hover:text-white dark:hover:text-white p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-slate-400 transition-colors">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-3">
                    @csrf
                    <div class="flex flex-col items-end">
                        <span class="text-white text-sm font-medium leading-none">{{ auth()->user()->name }}</span>
                        <button type="submit" class="text-xs text-indigo-200 hover:text-white dark:text-slate-400 dark:hover:text-slate-200 transition-colors mt-1">Sign out</button>
                    </div>
                </form>
            </div>

            <!-- Mobile Hamburger -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-indigo-200 hover:text-white focus:outline-none p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-cloak x-transition.opacity.duration.300ms class="md:hidden bg-indigo-800 dark:bg-slate-800 border-t border-indigo-700 dark:border-slate-700 absolute w-full shadow-lg">
            <div class="px-4 py-4 space-y-1">
                <p class="px-3 text-xs font-semibold text-indigo-300 dark:text-slate-400 uppercase tracking-wider mb-2">Dashboards</p>
                <a href="{{ route('dashboard.daily') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard.daily') ? 'bg-indigo-900 dark:bg-slate-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 dark:hover:bg-slate-700' }}">Daily Overview</a>
                <a href="{{ route('dashboard.sprint') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard.sprint') ? 'bg-indigo-900 dark:bg-slate-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 dark:hover:bg-slate-700' }}">Sprint Progress</a>
                
                <p class="px-3 text-xs font-semibold text-indigo-300 dark:text-slate-400 uppercase tracking-wider mt-5 mb-2">Management</p>
                <a href="{{ route('timer.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('timer.*') ? 'bg-indigo-900 dark:bg-slate-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 dark:hover:bg-slate-700' }}">Timer</a>
                <a href="{{ route('sprints.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('sprints.*') ? 'bg-indigo-900 dark:bg-slate-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 dark:hover:bg-slate-700' }}">Sprints</a>
                
                <p class="px-3 text-xs font-semibold text-indigo-300 dark:text-slate-400 uppercase tracking-wider mt-5 mb-2">Reports</p>
                <a href="{{ route('work-sessions.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('work-sessions.*') ? 'bg-indigo-900 dark:bg-slate-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 dark:hover:bg-slate-700' }}">Work Logs</a>
                <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('reports.index') ? 'bg-indigo-900 dark:bg-slate-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 dark:hover:bg-slate-700' }}">Weekly Summary</a>
                <a href="{{ route('reports.monthly') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('reports.monthly') ? 'bg-indigo-900 dark:bg-slate-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 dark:hover:bg-slate-700' }}">Monthly Analytics</a>
                
                <div class="border-t border-indigo-700 dark:border-slate-700 mt-5 pt-5 flex justify-between items-center px-3">
                    <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-indigo-200 hover:text-white dark:text-slate-400 dark:hover:text-slate-200 text-sm font-medium">Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="max-w-7xl mx-auto px-4 py-8" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 50)" x-show="loaded" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
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
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
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
