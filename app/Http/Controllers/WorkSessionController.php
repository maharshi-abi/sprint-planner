<?php

namespace App\Http\Controllers;

use App\Models\WorkSession;
use App\Services\DashboardService;
use App\Services\WorkSessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WorkSessionController extends Controller
{
    public function __construct(
        protected WorkSessionService $workSessions,
        protected DashboardService $dashboard,
    ) {}

    public function index(Request $request)
    {
        $query = WorkSession::with(['sprint', 'task', 'category'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->orderByDesc('started_at');

        if ($request->filled('date')) {
            $query->whereDate('started_at', $request->date('date'));
        }

        if ($request->filled('sprint_id')) {
            $query->where('sprint_id', $request->integer('sprint_id'));
        }

        $sessions = $query->paginate(15)->withQueryString();

        return view('work-sessions.index', [
            'sessions' => $sessions,
            'sprints' => $this->dashboard->sprintsForSelect($request->user()->id),
            'filterDate' => $request->input('date'),
            'filterSprintId' => $request->input('sprint_id'),
        ]);
    }

    public function create(Request $request)
    {
        return view('work-sessions.create', [
            'sprints' => $this->dashboard->sprintsForSelect($request->user()->id),
            'categories' => $this->dashboard->categories(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateSession($request);

        $this->workSessions->createManual($request->user()->id, $validated);

        return redirect()
            ->route('work-sessions.index', ['date' => Carbon::parse($validated['started_at'])->toDateString()])
            ->with('success', 'Work session logged successfully.');
    }

    public function edit(WorkSession $workSession, Request $request)
    {
        $this->authorizeSession($workSession, $request);

        return view('work-sessions.edit', [
            'session' => $workSession->load(['sprint', 'task', 'category']),
            'sprints' => $this->dashboard->sprintsForSelect($request->user()->id),
            'categories' => $this->dashboard->categories(),
            'tasks' => $this->dashboard->tasksForSprint($workSession->sprint_id),
        ]);
    }

    public function update(Request $request, WorkSession $workSession)
    {
        $this->authorizeSession($workSession, $request);

        $validated = $this->validateSession($request);

        $this->workSessions->updateCompleted($workSession, $validated);

        return redirect()
            ->route('work-sessions.index', ['date' => Carbon::parse($validated['started_at'])->toDateString()])
            ->with('success', 'Work session updated.');
    }

    public function destroy(WorkSession $workSession, Request $request)
    {
        $this->authorizeSession($workSession, $request);

        $date = $workSession->started_at->toDateString();
        $this->workSessions->deleteCompleted($workSession);

        return redirect()
            ->route('work-sessions.index', ['date' => $date])
            ->with('success', 'Work session removed.');
    }

    protected function validateSession(Request $request): array
    {
        $validated = $request->validate([
            'sprint_id' => ['required', 'exists:sprints,id'],
            'task_id' => ['required', 'exists:tasks,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date'],
            'interruption_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

        $startedAt = Carbon::parse($validated['started_at']);
        $endedAt = Carbon::parse($validated['ended_at']);

        if ($endedAt->lessThanOrEqualTo($startedAt)) {
            throw ValidationException::withMessages([
                'ended_at' => 'End date & time must be after start date & time (same day is allowed).',
            ]);
        }

        $validated['started_at'] = $startedAt->format('Y-m-d H:i:s');
        $validated['ended_at'] = $endedAt->format('Y-m-d H:i:s');
        $validated['interruption_seconds'] = ((int) ($validated['interruption_minutes'] ?? 0)) * 60;
        unset($validated['interruption_minutes']);

        return $validated;
    }

    protected function authorizeSession(WorkSession $session, Request $request): void
    {
        abort_unless($session->user_id === $request->user()->id, 403);
    }
}
