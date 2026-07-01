@extends('layouts.app')

@section('title', $sprint->name)

@section('content')
{{-- Title + actions (goal kept separate so buttons stay compact) --}}
<div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 truncate">{{ $sprint->name }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            {{ $sprint->start_date->format('M d') }} – {{ $sprint->end_date->format('M d, Y') }}
            <span class="mx-1">·</span>
            <span class="{{ $sprint->is_completed ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $sprint->is_completed ? 'Completed' : 'In progress' }}
            </span>
        </p>
    </div>
    <div class="flex flex-wrap gap-2 shrink-0">
        <a href="{{ route('tasks.create', $sprint) }}" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap">Add Task</a>
        <a href="{{ route('sprints.edit', $sprint) }}" class="inline-flex items-center justify-center border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:bg-slate-900 px-3 py-2 rounded-lg text-sm whitespace-nowrap">Edit</a>
        <a href="{{ route('dashboard.sprint', ['sprint_id' => $sprint->id]) }}" class="inline-flex items-center justify-center border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:bg-slate-900 px-3 py-2 rounded-lg text-sm whitespace-nowrap">Dashboard</a>
    </div>
</div>

@if(!empty($sprint->goal) && trim(strip_tags($sprint->goal)) !== '')
<div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-4 mb-6 max-h-48 overflow-y-auto">
    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Sprint Goal</p>
    @include('partials.sprint-goal', ['goal' => $sprint->goal, 'class' => 'text-sm text-slate-700 dark:text-slate-300'])
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @php
        $statuses = [
            'pending' => ['title' => 'Pending', 'colorClass' => 'text-slate-700 dark:text-slate-300'],
            'in_progress' => ['title' => 'In Progress', 'colorClass' => 'text-indigo-700 dark:text-indigo-300'],
            'completed' => ['title' => 'Completed', 'colorClass' => 'text-emerald-700 dark:text-emerald-300']
        ];
    @endphp
    
    @foreach($statuses as $statusKey => $statusMeta)
    <div class="flex flex-col bg-slate-100 dark:bg-slate-800/50 rounded-xl p-4 min-h-[500px] border border-slate-200 dark:border-slate-700">
        <h3 class="font-bold {{ $statusMeta['colorClass'] }} mb-4 flex items-center justify-between">
            {{ $statusMeta['title'] }}
            <span class="bg-white dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-xs py-1 px-2 border dark:border-slate-600 rounded-full shadow-sm">{{ $sprint->tasks->where('status', $statusKey)->count() }}</span>
        </h3>
        
        <div class="flex-1 space-y-3 sortable-list" data-status="{{ $statusKey }}">
            @foreach($sprint->tasks->where('status', $statusKey) as $task)
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-600 cursor-move premium-card relative group" data-id="{{ $task->id }}">
                <div class="flex justify-between items-start mb-3 gap-2">
                    <h4 class="font-semibold text-slate-800 dark:text-slate-100 leading-tight">{{ $task->title }}</h4>
                    <a href="{{ route('tasks.edit', $task) }}" class="opacity-0 group-hover:opacity-100 text-indigo-600 dark:text-indigo-400 hover:underline text-xs transition-opacity duration-200 shrink-0">Edit</a>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mt-auto">
                    <span class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded font-medium border border-slate-200 dark:border-slate-600">{{ $task->category?->name ?? 'No Category' }}</span>
                    <span class="font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-2 py-1 rounded border border-indigo-100 dark:border-indigo-800">
                        {{ $task->actual_hours }}h / {{ $task->estimated_hours }}h
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.querySelectorAll('.sortable-list').forEach(function(list) {
        new Sortable(list, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function (evt) {
                const itemEl = evt.item;
                const taskId = itemEl.getAttribute('data-id');
                const newStatus = evt.to.getAttribute('data-status');
                const oldStatus = evt.from.getAttribute('data-status');
                
                if (newStatus === oldStatus) return;
                
                // Update count badges locally
                const oldBadge = evt.from.previousElementSibling.querySelector('span');
                const newBadge = evt.to.previousElementSibling.querySelector('span');
                oldBadge.textContent = parseInt(oldBadge.textContent) - 1;
                newBadge.textContent = parseInt(newBadge.textContent) + 1;

                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                }).then(response => {
                    if (!response.ok) {
                        alert('Failed to update task status.');
                        // Revert manually if failed
                        evt.from.appendChild(itemEl);
                        oldBadge.textContent = parseInt(oldBadge.textContent) + 1;
                        newBadge.textContent = parseInt(newBadge.textContent) - 1;
                    }
                });
            },
        });
    });
</script>
@endpush
