@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ now()->format('l, F j, Y') }}</p>
    </div>
    @if($activeSession)
    <a href="{{ route('timer.index') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span></span>
        Timer Running
    </a>
    @else
    <a href="{{ route('timer.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Start Timer
    </a>
    @endif
</div>

{{-- Active timer banner --}}
@if($activeSession)
<div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl p-4 mb-6 flex items-center justify-between shadow-md">
    <div class="flex items-center gap-3">
        <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span></span>
        <div>
            <p class="font-semibold">Timer Running: {{ $activeSession->task?->title }}</p>
            <p class="text-emerald-100 text-sm">{{ $activeSession->sprint?->name }} · {{ $activeSession->category?->name }}</p>
        </div>
    </div>
    <a href="{{ route('timer.index') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Manage →</a>
</div>
@endif

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Today's Hours</p>
                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ $todayHours }}<span class="text-base font-normal text-slate-400">h</span></p>
            </div>
            <div class="bg-indigo-100 dark:bg-indigo-900/50 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">This Month: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $monthHours }}h</span></p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tasks Done</p>
                <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">{{ $completedTasks }}<span class="text-base font-normal text-slate-400">/{{ $totalTasks }}</span></p>
            </div>
            <div class="bg-emerald-100 dark:bg-emerald-900/50 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">In Progress: <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $inProgressTasks }}</span></p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Active Sprints</p>
                <p class="text-3xl font-bold text-violet-600 dark:text-violet-400 mt-2">{{ $activeSprints }}<span class="text-base font-normal text-slate-400">/{{ $totalSprints }}</span></p>
            </div>
            <div class="bg-violet-100 dark:bg-violet-900/50 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Pending Tasks: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $pendingTasks }}</span></p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Hours</p>
                <p class="text-3xl font-bold text-rose-600 dark:text-rose-400 mt-2">{{ $totalHours }}<span class="text-base font-normal text-slate-400">h</span></p>
            </div>
            <div class="bg-rose-100 dark:bg-rose-900/50 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Sessions: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $totalSessions }}</span></p>
    </div>
</div>

{{-- Charts row --}}
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <h2 class="font-semibold text-slate-800 dark:text-slate-200 mb-4">Last 7 Days — Hours Worked</h2>
        <div style="height: 220px;">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <h2 class="font-semibold text-slate-800 dark:text-slate-200 mb-4">Category Breakdown</h2>
        <div style="height: 220px;" class="flex items-center justify-center">
            @if($categoryBreakdown->isNotEmpty())
            <canvas id="categoryChart"></canvas>
            @else
            <p class="text-sm text-slate-400 dark:text-slate-500 text-center">No completed sessions yet.</p>
            @endif
        </div>
    </div>
</div>

{{-- Sprint progress + Quick links --}}
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    {{-- Active Sprints --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800 dark:text-slate-200">Active Sprints Progress</h2>
            <a href="{{ route('sprints.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">View all →</a>
        </div>
        <div class="p-5 space-y-4">
            @forelse($sprintSummaries as $s)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <a href="{{ route('sprints.show', $s['id']) }}" class="text-sm font-medium text-slate-800 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $s['name'] }}</a>
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $s['completed'] }}/{{ $s['total'] }} tasks · {{ $s['progress'] }}%</span>
                </div>
                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $s['progress'] >= 100 ? 'bg-emerald-500' : ($s['progress'] >= 50 ? 'bg-indigo-500' : 'bg-amber-500') }}" style="width: {{ $s['progress'] }}%"></div>
                </div>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $s['start_date'] }} – {{ $s['end_date'] }}</p>
            </div>
            @empty
            <p class="text-sm text-slate-400 dark:text-slate-500 text-center py-6">No active sprints. <a href="{{ route('sprints.create') }}" class="text-indigo-600 dark:text-indigo-400 underline">Create one</a></p>
            @endforelse
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <h2 class="font-semibold text-slate-800 dark:text-slate-200 mb-4">Quick Links</h2>
        <div class="grid grid-cols-2 gap-3">
            @php
            $links = [
                ['route' => 'timer.index', 'label' => 'Timer', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'indigo'],
                ['route' => 'sprints.index', 'label' => 'Sprints', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'violet'],
                ['route' => 'work-sessions.index', 'label' => 'Work Logs', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'emerald'],
                ['route' => 'reports.index', 'label' => 'Weekly Report', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'blue'],
                ['route' => 'reports.monthly', 'label' => 'Monthly', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'rose'],
                ['route' => 'dashboard.sprint', 'label' => 'Sprint View', 'icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z', 'color' => 'amber'],
            ];
            $colorMap = [
                'indigo' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50',
                'violet' => 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 hover:bg-violet-100 dark:hover:bg-violet-900/50',
                'emerald'=> 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50',
                'blue'   => 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50',
                'rose'   => 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50',
                'amber'  => 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50',
            ];
            @endphp
            @foreach($links as $link)
            <a href="{{ route($link['route']) }}" class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl text-center transition-colors {{ $colorMap[$link['color']] }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                <span class="text-xs font-semibold">{{ $link['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Recent Sessions --}}
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <h2 class="font-semibold text-slate-800 dark:text-slate-200">Recent Work Sessions</h2>
        <a href="{{ route('work-sessions.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-left">
                <tr>
                    <th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Task</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium text-right">Hours</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSessions as $session)
                <tr class="border-t border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-5 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $session->started_at->format('M d, H:i') }}</td>
                    <td class="px-5 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $session->task?->title ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $session->category?->color ?? '#94a3b8' }}"></span>
                            <span class="text-slate-600 dark:text-slate-400">{{ $session->category?->name ?? '—' }}</span>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right font-medium text-indigo-600 dark:text-indigo-400">
                        {{ $session->status === 'completed' ? $session->workedHours().'h' : '—' }}
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium capitalize
                            {{ $session->status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' :
                               ($session->status === 'active' ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300') }}">
                            {{ $session->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">No sessions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
const isDark = document.documentElement.classList.contains('dark');
const textColor = isDark ? '#f1f5f9' : '#1e293b';
const gridColor = isDark ? '#334155' : '#e2e8f0';

// Weekly bar chart
const weeklyData = @json($weeklyChartData);
new Chart(document.getElementById('weeklyChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(weeklyData),
        datasets: [{
            label: 'Hours',
            data: Object.values(weeklyData),
            backgroundColor: 'rgba(99,102,241,0.8)',
            borderColor: '#6366f1',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, ticks: { color: textColor }, grid: { color: gridColor } },
            x: { ticks: { color: textColor }, grid: { display: false } }
        },
        plugins: { legend: { display: false } }
    }
});

// Category doughnut
@if($categoryBreakdown->isNotEmpty())
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: @json($categoryBreakdown->pluck('category')),
        datasets: [{
            data: @json($categoryBreakdown->pluck('hours')),
            backgroundColor: @json($categoryBreakdown->pluck('color')),
            borderWidth: isDark ? 2 : 1,
            borderColor: isDark ? '#1e293b' : '#ffffff'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '68%',
        plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 10, usePointStyle: true, pointStyle: 'circle', boxWidth: 8 } } }
    }
});
@endif
</script>
@endpush
@endsection
