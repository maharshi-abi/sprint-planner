<?php

namespace App\Http\Controllers;

use App\Models\WorkSession;
use App\Services\DashboardService;
use App\Services\TimerService;
use Illuminate\Http\Request;

class TimerController extends Controller
{
    public function __construct(
        protected TimerService $timer,
        protected DashboardService $dashboard,
    ) {}

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        return view('timer.index', [
            'sprints'       => $this->dashboard->activeSprintsForTimer($userId),
            'categories'    => $this->dashboard->categories(),
            'activeSession' => $this->timer->activeSession($userId),
        ]);
    }

    public function tasks(Request $request)
    {
        $request->validate(['sprint_id' => ['required', 'exists:sprints,id']]);

        return response()->json(
            $this->dashboard->tasksForPendingSprint($request->integer('sprint_id'))
        );
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'sprint_id' => ['required', 'exists:sprints,id'],
            'task_id' => ['required', 'exists:tasks,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $session = $this->timer->start($request->user()->id, $validated);

        return redirect()->route('timer.index')->with('success', 'Timer started.');
    }

    public function pause(Request $request, WorkSession $workSession)
    {
        $this->authorizeSession($workSession, $request);

        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $this->timer->pause($workSession, $validated['reason'] ?? null);

        return redirect()->route('timer.index')->with('success', 'Timer paused (interruption logged).');
    }

    public function resume(Request $request, WorkSession $workSession)
    {
        $this->authorizeSession($workSession, $request);
        $this->timer->resume($workSession);

        return redirect()->route('timer.index')->with('success', 'Timer resumed.');
    }

    public function stop(Request $request, WorkSession $workSession)
    {
        $this->authorizeSession($workSession, $request);
        $this->timer->stop($workSession);

        return redirect()->route('dashboard.daily')->with('success', 'Work session completed.');
    }

    public function update(Request $request, WorkSession $workSession)
    {
        $this->authorizeSession($workSession, $request);

        $validated = $request->validate([
            'started_date'  => ['required', 'date'],
            'started_time'  => ['required', 'date_format:H:i'],
            'description'   => ['nullable', 'string', 'max:2000'],
        ]);

        $newStart = \Carbon\Carbon::parse($validated['started_date'] . ' ' . $validated['started_time']);

        // Don't allow start time in the future
        if ($newStart->isFuture()) {
            return back()->withErrors(['started_date' => 'Start time cannot be in the future.']);
        }

        $workSession->update([
            'started_at'  => $newStart,
            'description' => $validated['description'] ?? $workSession->description,
        ]);

        return redirect()->route('timer.index')->with('success', 'Session updated successfully.');
    }

    public function status(Request $request)
    {
        $session = $this->timer->activeSession($request->user()->id);

        if (! $session) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active' => true,
            'status' => $session->status,
            'elapsed_seconds' => $this->timer->tickElapsed($session),
            'task' => $session->task->title,
            'category' => $session->category->name,
            'sprint' => $session->sprint->name,
        ]);
    }

    protected function authorizeSession(WorkSession $session, Request $request): void
    {
        abort_unless($session->user_id === $request->user()->id, 403);
    }
}
