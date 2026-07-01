<?php

namespace App\Services;

use App\Models\Interruption;
use App\Models\Task;
use App\Models\WorkSession;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TimerService
{
    /**
     * Whole seconds from $start to $end (Carbon 3 diffInSeconds defaults to signed float).
     */
    protected function elapsedSecondsBetween(CarbonInterface $start, CarbonInterface $end): int
    {
        return max(0, (int) round($start->diffInSeconds($end, true)));
    }

    public function activeSession(int $userId): ?WorkSession
    {
        return WorkSession::with(['sprint', 'task', 'category', 'interruptions'])
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'paused'])
            ->latest('id')
            ->first();
    }

    public function start(int $userId, array $data): WorkSession
    {
        if ($this->activeSession($userId)) {
            throw ValidationException::withMessages([
                'timer' => 'Only one active timer is allowed. Stop or complete the current session first.',
            ]);
        }

        return DB::transaction(function () use ($userId, $data) {
            $session = WorkSession::create([
                'user_id' => $userId,
                'sprint_id' => $data['sprint_id'],
                'task_id' => $data['task_id'],
                'category_id' => $data['category_id'],
                'description' => $data['description'] ?? null,
                'started_at' => now(),
                'status' => 'active',
            ]);

            Task::where('id', $data['task_id'])->update(['status' => 'in_progress']);

            return $session->load(['sprint', 'task', 'category']);
        });
    }

    public function pause(WorkSession $session, ?string $reason = null): WorkSession
    {
        if ($session->status !== 'active') {
            throw ValidationException::withMessages(['timer' => 'Timer is not running.']);
        }

        $now = now();

        $session->update([
            'status' => 'paused',
            'paused_at' => $now,
        ]);

        Interruption::create([
            'work_session_id' => $session->id,
            'started_at' => $now,
            'reason' => $reason,
        ]);

        return $session->fresh(['sprint', 'task', 'category', 'interruptions']);
    }

    public function resume(WorkSession $session): WorkSession
    {
        if ($session->status !== 'paused') {
            throw ValidationException::withMessages(['timer' => 'Timer is not paused.']);
        }

        $openInterruption = $session->interruptions()->whereNull('ended_at')->latest('id')->first();

        if ($openInterruption) {
            $endedAt = now();
            $duration = $this->elapsedSecondsBetween($openInterruption->started_at, $endedAt);

            $openInterruption->update([
                'ended_at' => $endedAt,
                'duration_seconds' => $duration,
            ]);

            $session->increment('interruption_seconds', $duration);
        }

        $session->update([
            'status' => 'active',
            'paused_at' => null,
        ]);

        return $session->fresh(['sprint', 'task', 'category', 'interruptions']);
    }

    public function stop(WorkSession $session): WorkSession
    {
        if ($session->status === 'paused') {
            $this->resume($session);
            $session->refresh();
        }

        return $this->complete($session);
    }

    public function complete(WorkSession $session): WorkSession
    {
        if ($session->status === 'completed') {
            throw ValidationException::withMessages(['timer' => 'Session already completed.']);
        }

        return DB::transaction(function () use ($session) {
            $endedAt = now();
            $elapsed = $this->elapsedSecondsBetween($session->started_at, $endedAt);

            $session->update([
                'ended_at' => $endedAt,
                'elapsed_seconds' => $elapsed,
                'status' => 'completed',
                'paused_at' => null,
            ]);

            $session = $session->fresh(['sprint', 'task', 'category', 'interruptions']);

            app(WorkSessionService::class)->finalizeCompletedSession($session);

            return $session;
        });
    }

    public function tickElapsed(WorkSession $session): int
    {
        if ($session->status === 'completed') {
            return $session->workedSeconds();
        }

        $elapsed = $this->elapsedSecondsBetween($session->started_at, now());
        $interruption = (int) $session->interruption_seconds;

        if ($session->status === 'paused' && $session->paused_at) {
            $open = $session->interruptions()->whereNull('ended_at')->latest('id')->first();
            if ($open) {
                $interruption += $this->elapsedSecondsBetween($open->started_at, now());
            }
        }

        return max(0, $elapsed - $interruption);
    }
}
