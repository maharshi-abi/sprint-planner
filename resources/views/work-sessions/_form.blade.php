@php
    $started = old('started_at', isset($session) ? $session->started_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
    $ended = old('ended_at', isset($session) ? $session->ended_at->format('Y-m-d\TH:i') : now()->addHour()->format('Y-m-d\TH:i'));
    $interruptionMin = old('interruption_minutes', isset($session) ? (int) round($session->interruption_seconds / 60) : 0);
    $selectedSprint = old('sprint_id', $session?->sprint_id ?? request('sprint_id'));
    $selectedTask = old('task_id', $session?->task_id ?? '');
@endphp

<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Sprint</label>
        <select name="sprint_id" id="ws-sprint" required class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="">Select sprint</option>
            @foreach($sprints as $sprint)
                <option value="{{ $sprint->id }}" @selected($selectedSprint == $sprint->id)>{{ $sprint->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Task</label>
        <select name="task_id" id="ws-task" required class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="">Select sprint first</option>
            @if(isset($tasks))
                @foreach($tasks as $task)
                    <option value="{{ $task->id }}" @selected($selectedTask == $task->id)>{{ $task->title }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Category</label>
    <select name="category_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $session?->category_id) == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
</div>

<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Start date & time</label>
        <input type="datetime-local" name="started_at" value="{{ $started }}" required
            class="w-full border rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">End date & time</label>
        <input type="datetime-local" name="ended_at" value="{{ $ended }}" required
            class="w-full border rounded-lg px-3 py-2 text-sm">
    </div>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Interruption time (minutes)</label>
    <input type="number" name="interruption_minutes" value="{{ $interruptionMin }}" min="0" step="1"
        class="w-full border rounded-lg px-3 py-2 text-sm max-w-xs">
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Optional. Time not counted toward worked hours (meetings, breaks, etc.).</p>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Work description</label>
    <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('description', $session?->description) }}</textarea>
</div>

@push('scripts')
<script>
const sprintSelect = document.getElementById('ws-sprint');
const taskSelect = document.getElementById('ws-task');
if (sprintSelect && taskSelect) {
    async function loadTasks(sprintId, selectedTaskId) {
        taskSelect.innerHTML = '<option value="">Loading...</option>';
        if (!sprintId) {
            taskSelect.innerHTML = '<option value="">Select sprint first</option>';
            return;
        }
        const res = await fetch(`{{ route('timer.tasks') }}?sprint_id=${sprintId}`);
        const tasks = await res.json();
        taskSelect.innerHTML = '<option value="">Select task</option>';
        tasks.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.title;
            if (String(t.id) === String(selectedTaskId)) opt.selected = true;
            taskSelect.appendChild(opt);
        });
    }
    sprintSelect.addEventListener('change', () => loadTasks(sprintSelect.value, ''));
    if (sprintSelect.value) loadTasks(sprintSelect.value, '{{ $selectedTask }}');
}
</script>
@endpush
