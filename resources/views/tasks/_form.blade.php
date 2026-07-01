<form method="POST" action="{{ $action }}" class="bg-white dark:bg-slate-800 rounded-xl border p-6 shadow-sm max-w-2xl space-y-4">
    @csrf
    @if(!empty($method)) @method($method) @endif
    <div>
        <label class="block text-sm font-medium mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title', $task?->title) }}" required class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('description', $task?->description) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Category</label>
        <select name="category_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">— None —</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $task?->category_id) == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Estimated Hours</label>
            <input type="number" step="0.25" min="0" name="estimated_hours" value="{{ old('estimated_hours', $task?->estimated_hours ?? 1) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
        @if($task)
        <div>
            <label class="block text-sm font-medium mb-1">Actual Hours</label>
            <input type="number" step="0.25" min="0" name="actual_hours" value="{{ old('actual_hours', $task->actual_hours) }}" class="w-full border rounded-lg px-3 py-2">
        </div>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full border rounded-lg px-3 py-2">
            @foreach(['pending','in_progress','completed'] as $status)
                <option value="{{ $status }}" @selected(old('status', $task?->status ?? 'pending') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Save Task</button>
</form>
