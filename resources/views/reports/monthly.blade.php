@extends('layouts.app')

@section('title', 'Monthly Analytics')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Monthly Analytics</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($startDate)->format('F d') }} – {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
    </div>
    <a href="{{ route('reports.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Weekly Reports
    </a>
</div>

{{-- Date filter --}}
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('reports.monthly') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm w-44 focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm w-44 focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-6.414 6.414A1 1 0 0014 13.586V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.414a1 1 0 00-.293-.707L1.293 6.707A1 1 0 011 6V4z"/></svg>
                Apply Filter
            </button>
            <a href="{{ route('reports.monthly.pdf', request()->all()) }}" class="bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800 px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export PDF
            </a>
        </div>
    </form>
</div>

{{-- KPI Cards --}}
@php
    $totalHours  = array_sum($dailyStats);
    $totalPoints = array_sum($dailyPoints);
    $workDays    = count(array_filter($dailyStats, fn($h) => $h > 0));
    $avgHoursDay = $workDays > 0 ? round($totalHours / $workDays, 2) : 0;
    arsort($taskStats);
    $topTask     = !empty($taskStats) ? array_key_first($taskStats) : null;
    $topTaskHrs  = $topTask ? $taskStats[$topTask] : 0;

@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Hours</p>
                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ number_format($totalHours, 1) }}<span class="text-base font-normal text-slate-400">h</span></p>
            </div>
            <div class="bg-indigo-100 dark:bg-indigo-900/40 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Across {{ $workDays }} active day(s)</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Avg Hours/Day</p>
                <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">{{ $avgHoursDay }}<span class="text-base font-normal text-slate-400">h</span></p>
            </div>
            <div class="bg-emerald-100 dark:bg-emerald-900/40 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Per active work day</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Points Done</p>
                <p class="text-3xl font-bold text-violet-600 dark:text-violet-400 mt-2">{{ number_format($totalPoints, 1) }}<span class="text-base font-normal text-slate-400">pts</span></p>
            </div>
            <div class="bg-violet-100 dark:bg-violet-900/40 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Est. hours completed</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tasks Tracked</p>
                <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-2">{{ count($taskStats) }}</p>
            </div>
            <div class="bg-amber-100 dark:bg-amber-900/40 p-2.5 rounded-lg">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Unique tasks worked on</p>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-slate-800 dark:text-slate-200">Daily Hours Worked</h2>
            <span class="text-xs bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-full font-medium">{{ number_format($totalHours, 1) }}h total</span>
        </div>
        <div style="height: 240px;"><canvas id="monthlyChart"></canvas></div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-slate-800 dark:text-slate-200">Points Completed</h2>
            <span class="text-xs bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-full font-medium">{{ number_format($totalPoints, 1) }}pts total</span>
        </div>
        <div style="height: 240px;"><canvas id="pointsChart"></canvas></div>
    </div>
</div>

{{-- Task breakdown with inline bar --}}
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <h2 class="font-semibold text-slate-800 dark:text-slate-200">Task Breakdown</h2>
        @if(count($taskStats) > 0)
        <span class="text-xs text-slate-400 dark:text-slate-500">{{ count($taskStats) }} task(s) · {{ number_format($totalHours, 2) }}h total</span>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-900/50 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                    <th class="px-6 py-3">Task</th>
                    <th class="px-6 py-3">Progress</th>
                    <th class="px-6 py-3 text-right">Hours</th>
                    <th class="px-6 py-3 text-right">% Share</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($taskStats as $task => $hours)
                @php $share = $totalHours > 0 ? round($hours / $totalHours * 100) : 0; @endphp
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4 text-sm text-slate-800 dark:text-slate-200 font-medium max-w-xs">{{ $task }}</td>
                    <td class="px-6 py-4 w-48">
                        <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $share }}%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-semibold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">{{ number_format($hours, 2) }}h</td>
                    <td class="px-6 py-4 text-sm text-right text-slate-500 dark:text-slate-400">{{ $share }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        No work sessions found for this period.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($taskStats) > 0)
            <tfoot>
                <tr class="bg-slate-50 dark:bg-slate-900/50 font-semibold text-slate-800 dark:text-slate-200">
                    <td class="px-6 py-3.5 text-sm border-t border-slate-200 dark:border-slate-700">Total</td>
                    <td class="px-6 py-3.5 border-t border-slate-200 dark:border-slate-700"></td>
                    <td class="px-6 py-3.5 text-sm border-t border-slate-200 dark:border-slate-700 text-right text-indigo-600 dark:text-indigo-400">{{ number_format($totalHours, 2) }}h</td>
                    <td class="px-6 py-3.5 text-sm border-t border-slate-200 dark:border-slate-700 text-right text-slate-500">100%</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const isDarkMode  = document.documentElement.classList.contains('dark');
    const textColor   = isDarkMode ? '#f1f5f9' : '#1e293b';
    const gridColor   = isDarkMode ? '#334155' : '#e2e8f0';
    const tooltipBg   = isDarkMode ? '#1e293b' : '#ffffff';

    const sharedOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, ticks: { color: textColor }, grid: { color: gridColor } },
            x: { ticks: { color: textColor, maxRotation: 45 }, grid: { display: false } }
        },
        plugins: {
            legend: { labels: { color: textColor } },
            tooltip: { backgroundColor: tooltipBg, titleColor: textColor, bodyColor: textColor, borderColor: gridColor, borderWidth: 1 }
        }
    };

    const dailyStats = @json($dailyStats);
    const chart = new Chart(document.getElementById('monthlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: Object.keys(dailyStats),
            datasets: [{
                label: 'Hours Worked',
                data: Object.values(dailyStats),
                backgroundColor: 'rgba(99,102,241,0.8)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: sharedOptions
    });

    const dailyPoints = @json($dailyPoints);
    const pointsChart = new Chart(document.getElementById('pointsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: Object.keys(dailyPoints),
            datasets: [{
                label: 'Points Completed',
                data: Object.values(dailyPoints),
                backgroundColor: 'rgba(16,185,129,0.15)',
                borderColor: '#10b981',
                borderWidth: 2.5,
                pointBackgroundColor: '#10b981',
                pointRadius: 5,
                tension: 0.4,
                fill: true,
            }]
        },
        options: { ...sharedOptions, scales: { ...sharedOptions.scales, y: { ...sharedOptions.scales.y, ticks: { ...sharedOptions.scales.y.ticks, stepSize: 1 } } } }
    });

    document.getElementById('theme-toggle')?.addEventListener('click', () => {
        setTimeout(() => {
            const isDark = document.documentElement.classList.contains('dark');
            const nc = isDark ? '#f1f5f9' : '#1e293b';
            const ng = isDark ? '#334155' : '#e2e8f0';
            [chart, pointsChart].forEach(c => {
                c.options.scales.y.ticks.color = nc;
                c.options.scales.y.grid.color  = ng;
                c.options.scales.x.ticks.color = nc;
                c.options.plugins.legend.labels.color = nc;
                c.update();
            });
        }, 10);
    });
</script>
@endpush
