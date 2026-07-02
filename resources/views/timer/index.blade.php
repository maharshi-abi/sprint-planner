@extends('layouts.app')

@section('title', 'Work Timer')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-bold">Work Session Timer</h1>
    <a href="{{ route('work-sessions.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Work log — edit or add past sessions</a>
</div>

@if($activeSession)
<div class="bg-indigo-50 dark:bg-slate-800 border-2 border-indigo-200 dark:border-slate-700 rounded-2xl p-6 mb-6 shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-8">
        <div class="flex-1 text-center md:text-left">
            <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium uppercase tracking-wide">Active Session</p>
            <h2 class="text-2xl font-bold mt-2">{{ $activeSession->task->title }}</h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">{{ $activeSession->sprint->name }} · {{ $activeSession->category->name }}</p>
            @if($activeSession->description)
                <p class="text-sm mt-3 text-slate-700 dark:text-slate-300 italic">"{{ $activeSession->description }}"</p>
            @endif
            <p class="text-sm capitalize mt-4 inline-block px-3 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-200">
                Status: <span id="timerStatus" class="font-bold">{{ $activeSession->status }}</span>
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                Started: {{ $activeSession->started_at->format('M d, Y H:i') }}
            </p>
        </div>
        
        <div class="relative flex items-center justify-center shrink-0">
            <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 200 200">
                <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="8" class="text-indigo-100 dark:text-indigo-900/50" />
                <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="8" class="text-indigo-600 dark:text-indigo-500 transition-all duration-1000 ease-linear" stroke-dasharray="565.48" stroke-dashoffset="0" id="timerRing" />
            </svg>
            <div class="absolute flex flex-col items-center justify-center pointer-events-none">
                <p class="text-4xl font-mono font-bold text-slate-800 dark:text-slate-100 drop-shadow-md" id="elapsedDisplay">00:00:00</p>
            </div>
        </div>
    </div>
    
    <div class="mt-8 flex flex-wrap items-center justify-center md:justify-start gap-3">
        @if($activeSession->status === 'active')
        <form method="POST" action="{{ route('timer.pause', $activeSession) }}" class="flex gap-2 items-center">
            @csrf
            <input type="text" name="reason" placeholder="Interruption reason (optional)" class="border border-indigo-200 dark:border-indigo-800 bg-white dark:bg-slate-900 rounded-full px-4 py-2.5 text-sm w-48 shadow-sm focus:ring-2 focus:ring-amber-500">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-sm transition-transform hover:scale-105">Pause</button>
        </form>
        @else
        <form method="POST" action="{{ route('timer.resume', $activeSession) }}">
            @csrf
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-2.5 rounded-full text-sm font-semibold shadow-sm transition-transform hover:scale-105">Resume</button>
        </form>
        @endif
        <form method="POST" action="{{ route('timer.stop', $activeSession) }}" onsubmit="return confirm('Complete this work session?')">
            @csrf
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-full text-sm font-semibold shadow-sm transition-transform hover:scale-105">Complete Session</button>
        </form>
        <button onclick="document.getElementById('editPanel').classList.toggle('hidden')" class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-5 py-2.5 rounded-full text-sm font-semibold transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Session
        </button>
    </div>

    {{-- Edit Panel (hidden by default) --}}
    <div id="editPanel" class="hidden mt-6 border-t border-indigo-200 dark:border-slate-700 pt-5">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Update Session Details</h3>
        <form method="POST" action="{{ route('timer.update', $activeSession) }}" class="grid sm:grid-cols-3 gap-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Start Date</label>
                <input type="date" name="started_date" value="{{ $activeSession->started_at->format('Y-m-d') }}"
                    class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Start Time</label>
                <input type="time" name="started_time" value="{{ $activeSession->started_at->format('H:i') }}"
                    class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500" placeholder="What are you working on?">{{ $activeSession->description }}</textarea>
            </div>
            <div class="sm:col-span-3 flex gap-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">Save Changes</button>
                <button type="button" onclick="document.getElementById('editPanel').classList.add('hidden')" class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-sm font-medium">Cancel</button>
            </div>
        </form>
    </div>

    @if($activeSession->interruptions->isNotEmpty())
    <div class="mt-6 border-t border-indigo-200 dark:border-slate-700 pt-4">
        <h3 class="text-sm font-semibold text-indigo-800 dark:text-indigo-200 mb-2">Interruption Log</h3>
        <ul class="text-sm space-y-1">
            @foreach($activeSession->interruptions as $i)
            <li class="text-slate-700 dark:text-slate-300">
                {{ $i->started_at->format('H:i') }}
                @if($i->ended_at) – {{ $i->ended_at->format('H:i') }} ({{ gmdate('H:i:s', $i->duration_seconds) }}) @else – ongoing @endif
                {{ $i->reason ? '· '.$i->reason : '' }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@else
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm max-w-2xl">
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Workflow: Select Sprint → Task → (Category auto-fills) → Description → Start Timer</p>
    <form method="POST" action="{{ route('timer.start') }}" class="space-y-4" id="timerForm">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Sprint</label>
            <select name="sprint_id" id="sprintSelect" required class="w-full border rounded-lg px-3 py-2">
                <option value="">— Select active sprint —</option>
                @foreach($sprints as $i => $sprint)
                    <option value="{{ $sprint->id }}" {{ $i === 0 ? 'selected' : '' }}>
                        {{ $sprint->name }}
                        ({{ $sprint->start_date->format('M d') }} – {{ $sprint->end_date->format('M d') }})
                    </option>
                @endforeach
            </select>
            @if($sprints->isEmpty())
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">No active sprints found. <a href="{{ route('sprints.create') }}" class="underline">Create one</a>.</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Task</label>
            <select name="task_id" id="taskSelect" required class="w-full border rounded-lg px-3 py-2" disabled>
                <option value="">Select sprint first</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Category <span id="categoryNote" class="text-xs text-indigo-500 dark:text-indigo-400 font-normal">(auto-filled from task)</span></label>
            <select name="category_id" id="categorySelect" required class="w-full border rounded-lg px-3 py-2">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" data-name="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Work Description</label>
            <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="What are you working on?"></textarea>
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium" @disabled($sprints->isEmpty())>
            Start Timer
        </button>
        @if($sprints->isEmpty())
            <p class="text-sm text-amber-600">Create a sprint and task first.</p>
        @endif
    </form>
</div>
@endif

@push('scripts')
<script>
function formatElapsed(seconds) {
    const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
    const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
    const s = Math.floor(seconds % 60).toString().padStart(2, '0');
    return `${h}:${m}:${s}`;
}

@if($activeSession)
const ring = document.getElementById('timerRing');
const circumference = 565.48;

async function refreshTimer() {
    const res = await fetch('{{ route('timer.status') }}');
    const data = await res.json();
    if (data.active) {
        document.getElementById('elapsedDisplay').textContent = formatElapsed(data.elapsed_seconds);
        document.getElementById('timerStatus').textContent = data.status;
        const seconds = data.elapsed_seconds % 60;
        const offset = circumference - (seconds / 60) * circumference;
        ring.style.strokeDashoffset = offset;
    }
}
setInterval(refreshTimer, 1000);
refreshTimer();
@endif

// Task select → auto-populate category
const sprintSelect = document.getElementById('sprintSelect');
const taskSelect   = document.getElementById('taskSelect');
const categorySelect = document.getElementById('categorySelect');

if (sprintSelect && taskSelect) {
    async function loadTasksForSprint(sprintId) {
        if (!sprintId) {
            taskSelect.innerHTML = '<option value="">Select sprint first</option>';
            taskSelect.disabled = true;
            return;
        }
        taskSelect.innerHTML = '<option value="">Loading tasks...</option>';
        taskSelect.disabled = true;
        const res   = await fetch(`{{ route('timer.tasks') }}?sprint_id=${sprintId}`);
        const tasks = await res.json();
        taskSelect.innerHTML = '<option value="">— Select task —</option>';
        tasks.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.title;
            opt.dataset.categoryId = t.category_id ?? '';
            taskSelect.appendChild(opt);
        });
        taskSelect.disabled = tasks.length === 0;
        if (tasks.length === 0) {
            taskSelect.innerHTML = '<option value="">No pending tasks in this sprint</option>';
        }
    }

    sprintSelect.addEventListener('change', () => loadTasksForSprint(sprintSelect.value));

    taskSelect.addEventListener('change', () => {
        const selected = taskSelect.options[taskSelect.selectedIndex];
        const catId = selected?.dataset?.categoryId;
        if (catId && categorySelect) {
            for (let i = 0; i < categorySelect.options.length; i++) {
                if (categorySelect.options[i].value == catId) {
                    categorySelect.selectedIndex = i;
                    break;
                }
            }
        }
    });

    // Auto-load tasks for the pre-selected sprint on page load
    if (sprintSelect.value) {
        loadTasksForSprint(sprintSelect.value);
    }
}
</script>
@endpush
@endsection
