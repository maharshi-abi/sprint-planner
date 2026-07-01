<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Support\SprintGoalFormatter;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index(Request $request)
    {
        $sprints = Sprint::where('user_id', $request->user()->id)
            ->withCount('tasks')
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('sprints.index', compact('sprints'));
    }

    public function create()
    {
        return view('sprints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $validated['goal'] = SprintGoalFormatter::sanitize($validated['goal'] ?? null);
        $request->user()->sprints()->create($validated);

        return redirect()->route('sprints.index')->with('success', 'Sprint created successfully.');
    }

    public function show(Sprint $sprint, Request $request)
    {
        $this->authorizeSprint($sprint, $request);
        $sprint->load(['tasks.category']);

        return view('sprints.show', compact('sprint'));
    }

    public function edit(Sprint $sprint, Request $request)
    {
        $this->authorizeSprint($sprint, $request);

        return view('sprints.edit', compact('sprint'));
    }

    public function update(Request $request, Sprint $sprint)
    {
        $this->authorizeSprint($sprint, $request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_completed' => ['sometimes', 'boolean'],
        ]);

        $validated['is_completed'] = $request->boolean('is_completed');
        $validated['goal'] = SprintGoalFormatter::sanitize($validated['goal'] ?? null);
        $sprint->update($validated);

        return redirect()->route('sprints.show', $sprint)->with('success', 'Sprint updated.');
    }

    public function destroy(Sprint $sprint, Request $request)
    {
        $this->authorizeSprint($sprint, $request);
        $sprint->delete();

        return redirect()->route('sprints.index')->with('success', 'Sprint deleted.');
    }

    protected function authorizeSprint(Sprint $sprint, Request $request): void
    {
        abort_unless($sprint->user_id === $request->user()->id, 403);
    }
}
