<?php

namespace App\Services;

use App\Models\Sprint;
use App\Models\Task;
use App\Models\WorkSession;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkSessionService
{
    public function __construct(protected TimerService $timer) {}

    protected function elapsedSecondsBetween(CarbonInterface $start, CarbonInterface $end): int
    {
        return max(0, (int) round($start->diffInSeconds($end, true)));
    }

    public function syncTaskActualHours(int $taskId): void
    {
        $hours = WorkSession::where('task_id', $taskId)
            ->where('status', 'completed')
            ->get()
            ->sum(fn (WorkSession $s) => $s->workedHours());

        Task::where('id', $taskId)->update(['actual_hours' => round($hours, 2)]);
    }

    public function recalculateDailySummaries(int $userId, array $dates): void
    {
        $daily = app(DailySummaryService::class);
        foreach (array_unique(array_filter($dates)) as $date) {
            $daily->recalculateForUser($userId, $date);
        }
    }

    public function finalizeCompletedSession(WorkSession $session, ?string $previousDate = null, ?int $previousTaskId = null): void
    {
        $dates = [
            $previousDate,
            $session->started_at->toDateString(),
        ];
        $taskIds = array_filter([$previousTaskId, $session->task_id]);

        foreach (array_unique($taskIds) as $taskId) {
            $this->syncTaskActualHours((int) $taskId);
        }

        $this->recalculateDailySummaries($session->user_id, $dates);
    }

    public function createManual(int $userId, array $data): WorkSession
    {
        if ($this->timer->activeSession($userId)) {
            throw ValidationException::withMessages([
                'timer' => 'Stop the active timer before adding a manual entry.',
            ]);
        }

        $this->authorizeSprintForUser($data['sprint_id'], $userId);
        $this->authorizeTaskForSprint($data['task_id'], $data['sprint_id']);

        return DB::transaction(function () use ($userId, $data) {
            $startedAt = Carbon::parse($data['started_at']);
            $endedAt = Carbon::parse($data['ended_at']);
            $interruptionSeconds = (int) ($data['interruption_seconds'] ?? 0);
            $elapsed = $this->elapsedSecondsBetween($startedAt, $endedAt);

            if ($interruptionSeconds > $elapsed) {
                throw ValidationException::withMessages([
                    'interruption_minutes' => 'Interruption time cannot exceed total session duration.',
                ]);
            }

            $session = WorkSession::create([
                'user_id' => $userId,
                'sprint_id' => $data['sprint_id'],
                'task_id' => $data['task_id'],
                'category_id' => $data['category_id'],
                'description' => $data['description'] ?? null,
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'elapsed_seconds' => $elapsed,
                'interruption_seconds' => $interruptionSeconds,
                'status' => 'completed',
                'paused_at' => null,
            ]);

            $this->finalizeCompletedSession($session);

            return $session->load(['sprint', 'task', 'category']);
        });
    }

    public function updateCompleted(WorkSession $session, array $data): WorkSession
    {
        if ($session->status !== 'completed') {
            throw ValidationException::withMessages([
                'session' => 'Only completed sessions can be edited here. Use the live timer for active sessions.',
            ]);
        }

        $previousDate = $session->started_at->toDateString();
        $previousTaskId = $session->task_id;

        $this->authorizeSprintForUser($data['sprint_id'], $session->user_id);
        $this->authorizeTaskForSprint($data['task_id'], $data['sprint_id']);

        return DB::transaction(function () use ($session, $data, $previousDate, $previousTaskId) {
            $startedAt = Carbon::parse($data['started_at']);
            $endedAt = Carbon::parse($data['ended_at']);
            $interruptionSeconds = (int) ($data['interruption_seconds'] ?? 0);
            $elapsed = $this->elapsedSecondsBetween($startedAt, $endedAt);

            if ($interruptionSeconds > $elapsed) {
                throw ValidationException::withMessages([
                    'interruption_minutes' => 'Interruption time cannot exceed total session duration.',
                ]);
            }

            $session->update([
                'sprint_id' => $data['sprint_id'],
                'task_id' => $data['task_id'],
                'category_id' => $data['category_id'],
                'description' => $data['description'] ?? null,
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'elapsed_seconds' => $elapsed,
                'interruption_seconds' => $interruptionSeconds,
                'paused_at' => null,
            ]);

            $this->finalizeCompletedSession($session->fresh(), $previousDate, $previousTaskId);

            return $session->fresh(['sprint', 'task', 'category']);
        });
    }

    public function deleteCompleted(WorkSession $session): void
    {
        if ($session->status !== 'completed') {
            throw ValidationException::withMessages([
                'session' => 'Only completed sessions can be deleted. Stop the timer first for active sessions.',
            ]);
        }

        DB::transaction(function () use ($session) {
            $userId = $session->user_id;
            $date = $session->started_at->toDateString();
            $taskId = $session->task_id;

            $session->interruptions()->delete();
            $session->delete();

            $this->syncTaskActualHours($taskId);
            $this->recalculateDailySummaries($userId, [$date]);
        });
    }

    protected function authorizeSprintForUser(int $sprintId, int $userId): void
    {
        abort_unless(
            Sprint::where('id', $sprintId)->where('user_id', $userId)->exists(),
            403
        );
    }

    protected function authorizeTaskForSprint(int $taskId, int $sprintId): void
    {
        abort_unless(
            Task::where('id', $taskId)->where('sprint_id', $sprintId)->exists(),
            403
        );
    }
}
