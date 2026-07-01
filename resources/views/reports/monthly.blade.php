@extends('layouts.app')

@section('title', 'Monthly Report')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Monthly Report</h1>
</div>

<div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('reports.monthly') }}" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-48 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-48 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition-colors">
                Apply Filter
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Chart -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Hours Completed (Timeline)</h2>
        <div style="height: 250px;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Points Chart -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Points Completed (Day by Day)</h2>
        <div style="height: 250px;">
            <canvas id="pointsChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-6">

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Task Summary</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50">
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">Task</th>
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 text-right">Total Hours</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($taskStats as $task => $hours)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $task }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100 text-right font-medium">{{ number_format($hours, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 text-center">No work sessions found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($taskStats) > 0)
                <tfoot>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 font-semibold text-slate-900 dark:text-slate-100">
                        <td class="px-6 py-4 text-sm border-t border-slate-200 dark:border-slate-700">Total</td>
                        <td class="px-6 py-4 text-sm border-t border-slate-200 dark:border-slate-700 text-right">{{ number_format(array_sum($taskStats), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    const dailyStats = @json($dailyStats);
    const labels = Object.keys(dailyStats);
    const data = Object.values(dailyStats);

    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#f1f5f9' : '#1e293b';
    const gridColor = isDarkMode ? '#334155' : '#e2e8f0';

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hours Worked',
                data: data,
                backgroundColor: '#4f46e5',
                borderColor: '#4338ca',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    labels: { color: textColor }
                }
            }
        }
    });

    const ctxPoints = document.getElementById('pointsChart').getContext('2d');
    
    const dailyPoints = @json($dailyPoints);
    const pointsLabels = Object.keys(dailyPoints);
    const pointsData = Object.values(dailyPoints);

    const pointsChart = new Chart(ctxPoints, {
        type: 'bar',
        data: {
            labels: pointsLabels,
            datasets: [{
                label: 'Points Completed',
                data: pointsData,
                backgroundColor: '#10b981', // emerald-500
                borderColor: '#059669', // emerald-600
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, stepSize: 1 },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    labels: { color: textColor }
                }
            }
        }
    });

    document.getElementById('theme-toggle').addEventListener('click', () => {
        setTimeout(() => {
            const isDark = document.documentElement.classList.contains('dark');
            const newTextColor = isDark ? '#f1f5f9' : '#1e293b';
            const newGridColor = isDark ? '#334155' : '#e2e8f0';

            chart.options.scales.y.ticks.color = newTextColor;
            chart.options.scales.y.grid.color = newGridColor;
            chart.options.scales.x.ticks.color = newTextColor;
            chart.options.plugins.legend.labels.color = newTextColor;
            chart.update();

            pointsChart.options.scales.y.ticks.color = newTextColor;
            pointsChart.options.scales.y.grid.color = newGridColor;
            pointsChart.options.scales.x.ticks.color = newTextColor;
            pointsChart.options.plugins.legend.labels.color = newTextColor;
            pointsChart.update();
        }, 10);
    });
</script>
@endpush
