<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Models\Task;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(protected DashboardService $dashboard) {}

    public function create(Sprint $sprint, Request $request)
    {
        abort_unless($sprint->user_id === $request->user()->id, 403);

        return view('tasks.create', [
            'sprint' => $sprint,
            'categories' => $this->dashboard->categories(),
        ]);
    }

    public function store(Request $request, Sprint $sprint)
    {
        abort_unless($sprint->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'estimated_hours' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $sprint->tasks()->create($validated);

        return redirect()->route('sprints.show', $sprint)->with('success', 'Task added.');
    }

    public function edit(Task $task, Request $request)
    {
        abort_unless($task->sprint->user_id === $request->user()->id, 403);

        return view('tasks.edit', [
            'task' => $task->load('sprint'),
            'categories' => $this->dashboard->categories(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        abort_unless($task->sprint->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'estimated_hours' => ['required', 'numeric', 'min:0'],
            'actual_hours' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $task->update($validated);

        return redirect()->route('sprints.show', $task->sprint_id)->with('success', 'Task updated.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        abort_unless($task->sprint->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task, Request $request)
    {
        abort_unless($task->sprint->user_id === $request->user()->id, 403);
        $sprintId = $task->sprint_id;
        $task->delete();

        return redirect()->route('sprints.show', $sprintId)->with('success', 'Task removed.');
    }
}
