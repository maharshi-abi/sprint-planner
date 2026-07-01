@extends('layouts.app')

@section('title', 'Sprint Dashboard')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold">Weekly Sprint Dashboard</h1>
    @if($sprints->isNotEmpty())
    <form method="GET" class="flex items-center gap-2">
        <select name="sprint_id" onchange="this.form.submit()" class="rounded-lg border px-3 py-2 text-sm">
            @foreach($sprints as $s)
                <option value="{{ $s->id }}" @selected($sprint && $sprint->id === $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
    </form>
    @endif
</div>

@if(!$sprint)
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-8 text-center text-slate-600 dark:text-slate-400">
        <p class="mb-4">No sprint found.</p>
        <a href="{{ route('sprints.create') }}" class="inline-block bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
            Create your first sprint
        </a>
    </div>
@else
<div class="grid md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-5 shadow-sm md:col-span-1">
        <p class="text-sm text-slate-500 dark:text-slate-400">Estimated</p>
        <p class="text-2xl font-bold">{{ $totalEstimated }}h</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500 dark:text-slate-400">Actual Logged</p>
        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalActual }}h</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500 dark:text-slate-400">Completed Tasks</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $completedCount }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500 dark:text-slate-400">Pending Tasks</p>
        <p class="text-2xl font-bold text-amber-600">{{ $pendingCount }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500 dark:text-slate-400">Estimation Accuracy</p>
        <p class="text-2xl font-bold">{{ $estimationAccuracy !== null ? $estimationAccuracy.'%' : 'N/A' }}</p>
    </div>
</div>

<div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 rounded-xl p-4 mb-6">
    <h2 class="font-semibold text-indigo-900 dark:text-indigo-100">{{ $sprint->name }}</h2>
    <div class="mt-1">@include('partials.sprint-goal', ['goal' => $sprint->goal, 'class' => 'text-sm text-indigo-800 dark:text-indigo-200'])</div>
    <p class="text-xs text-indigo-600 dark:text-indigo-300 mt-2">{{ $sprint->start_date->format('M d') }} – {{ $sprint->end_date->format('M d, Y') }}
        · {{ $sprint->is_completed ? 'Completed' : 'In Progress' }}</p>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-5 shadow-sm">
        <h3 class="font-semibold mb-3">Hours by Category</h3>
        <canvas id="sprintCategoryChart" height="200"></canvas>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border p-5 shadow-sm">
        <h3 class="font-semibold mb-3">Estimation vs Actual (Tasks)</h3>
        <canvas id="taskEstimationChart" height="200"></canvas>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6 mt-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border shadow-sm">
        <div class="px-4 py-3 border-b font-semibold text-emerald-700">Completed</div>
        <ul class="divide-y">
            @forelse($completedTasks as $task)
            <li class="px-4 py-3 text-sm flex justify-between">
                <span>{{ $task->title }}</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $task->actual_hours }}h / {{ $task->estimated_hours }}h est.</span>
            </li>
            @empty
            <li class="px-4 py-6 text-slate-500 dark:text-slate-400 text-sm text-center">None yet</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border shadow-sm">
        <div class="px-4 py-3 border-b font-semibold text-amber-700">Pending</div>
        <ul class="divide-y">
            @forelse($pendingTasks as $task)
            <li class="px-4 py-3 text-sm flex justify-between">
                <span>{{ $task->title }}</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $task->estimated_hours }}h est.</span>
            </li>
            @empty
            <li class="px-4 py-6 text-slate-500 dark:text-slate-400 text-sm text-center">All done!</li>
            @endforelse
        </ul>
    </div>
</div>
@endif

@if($sprint ?? false)
@push('scripts')
<script>
const catLabels = @json($categoryChart->pluck('label'));
const catHours = @json($categoryChart->pluck('hours'));
const catColors = @json($categoryChart->pluck('color'));
if (catLabels.length) {
    new Chart(document.getElementById('sprintCategoryChart'), {
        type: 'bar',
        data: { labels: catLabels, datasets: [{ label: 'Hours', data: catHours, backgroundColor: catColors }] },
        options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });
}
const tasks = @json($taskChart);
new Chart(document.getElementById('taskEstimationChart'), {
    type: 'bar',
    data: {
        labels: tasks.map(t => t.title),
        datasets: [
            { label: 'Estimated', data: tasks.map(t => t.estimated), backgroundColor: '#94a3b8' },
            { label: 'Actual', data: tasks.map(t => t.actual), backgroundColor: '#4f46e5' }
        ]
    },
    options: { scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endif
@endsection
