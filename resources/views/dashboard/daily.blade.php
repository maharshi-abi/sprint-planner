@extends('layouts.app')

@section('title', 'Daily Dashboard')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Daily Dashboard</h1>
    <a href="{{ route('timer.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Start Timer</a>
</div>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <p class="text-sm text-slate-500 dark:text-slate-400">Daily Target</p>
        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $summary->target_hours }}h</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <p class="text-sm text-slate-500 dark:text-slate-400">Completed</p>
        <p id="completedHours" class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $liveStats['completed_hours'] }}h</p>
        @if($activeTimer)
            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">Includes active timer (live)</p>
        @endif
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <p class="text-sm text-slate-500 dark:text-slate-400">Remaining</p>
        <p id="remainingHours" class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $liveStats['remaining_hours'] }}h</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <p class="text-sm text-slate-500 dark:text-slate-400">Progress</p>
        <p id="progressPercent" class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $liveStats['progress_percent'] }}%</p>
        <div class="mt-2 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
            <div id="progressBar" class="h-full bg-indigo-500 rounded-full transition-all duration-500" style="width: {{ $liveStats['progress_percent'] }}%"></div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <h2 class="font-semibold mb-3">Current Active Timer</h2>
        @if($activeTimer)
            <div class="border-l-4 border-indigo-500 pl-4">
                <p class="font-medium">{{ $activeTimer->task->title }}</p>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $activeTimer->sprint->name }} · {{ $activeTimer->category->name }}</p>
                <p class="text-sm mt-1">
                    Status: <span class="font-semibold {{ $activeTimer->status === 'paused' ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ ucfirst($activeTimer->status) }}</span>
                </p>
                <p class="text-sm font-mono mt-2" id="activeTimerElapsed">00:00:00</p>
                <a href="{{ route('timer.index') }}" class="inline-block mt-3 text-indigo-600 dark:text-indigo-400 text-sm font-medium">Manage timer →</a>
            </div>
        @else
            <p class="text-slate-500 dark:text-slate-400 text-sm">No active timer. Start a work session to track your 8-hour day.</p>
        @endif
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm premium-card">
        <h2 class="font-semibold mb-3">Category Breakdown (Today)</h2>
        <div class="relative w-full h-48 flex justify-center items-center">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl border shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b font-semibold">Recent Activities</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900 text-left text-slate-600 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3">When</th>
                    <th class="px-4 py-3">Sprint</th>
                    <th class="px-4 py-3">Task</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Hours</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActivities as $activity)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $activity->started_at->format('M d, H:i') }}</td>
                    <td class="px-4 py-3">{{ $activity->sprint->name }}</td>
                    <td class="px-4 py-3">{{ $activity->task->title }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $activity->category->color }}"></span>
                            {{ $activity->category->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($activity->status === 'completed')
                            {{ $activity->workedHours() }}h
                        @elseif(in_array($activity->status, ['active', 'paused']))
                            <span class="text-emerald-600">In progress</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 capitalize">{{ $activity->status }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        @if($activity->status === 'completed')
                            <a href="{{ route('work-sessions.edit', $activity) }}" class="text-indigo-600 dark:text-indigo-400 text-xs hover:underline">Edit time</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No work sessions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function formatElapsed(seconds) {
    const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
    const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
    const s = Math.floor(seconds % 60).toString().padStart(2, '0');
    return `${h}:${m}:${s}`;
}

const labels = @json($categoryBreakdown->pluck('category'));
const data = @json($categoryBreakdown->pluck('hours'));
const colors = @json($categoryBreakdown->pluck('color'));
if (labels.length) {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#f1f5f9' : '#1e293b';
    
    const catChart = new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: isDark ? 2 : 1, borderColor: isDark ? '#1e293b' : '#ffffff' }] },
        options: { 
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { 
                legend: { 
                    position: 'right',
                    labels: { color: textColor, padding: 15, usePointStyle: true, pointStyle: 'circle' }
                } 
            }
        }
    });

    document.getElementById('theme-toggle').addEventListener('click', () => {
        setTimeout(() => {
            const isDarkNow = document.documentElement.classList.contains('dark');
            catChart.options.plugins.legend.labels.color = isDarkNow ? '#f1f5f9' : '#1e293b';
            catChart.data.datasets[0].borderColor = isDarkNow ? '#1e293b' : '#ffffff';
            catChart.data.datasets[0].borderWidth = isDarkNow ? 2 : 1;
            catChart.update();
        }, 10);
    });
}

async function refreshDailyStats() {
    const res = await fetch('{{ route('dashboard.daily.stats') }}');
    const stats = await res.json();
    document.getElementById('completedHours').textContent = stats.completed_hours + 'h';
    document.getElementById('remainingHours').textContent = stats.remaining_hours + 'h';
    document.getElementById('progressPercent').textContent = stats.progress_percent + '%';
    document.getElementById('progressBar').style.width = stats.progress_percent + '%';

    @if($activeTimer)
    const timerRes = await fetch('{{ route('timer.status') }}');
    const timer = await timerRes.json();
    if (timer.active) {
        const el = document.getElementById('activeTimerElapsed');
        if (el) el.textContent = formatElapsed(timer.elapsed_seconds);
    }
    @endif
}

@if($activeTimer)
setInterval(refreshDailyStats, 1000);
refreshDailyStats();
@else
setInterval(refreshDailyStats, 30000);
@endif
</script>
@endpush
@endsection
