<?php

namespace App\Services;

use App\Models\DailySummary;
use App\Models\WorkSession;
use Carbon\Carbon;

class DailySummaryService
{
    public const DAILY_TARGET_HOURS = 8;

    public function __construct(protected TimerService $timer) {}

    public function completedSecondsForDate(int $userId, string $date, bool $includeActive = true): int
    {
        $completedSeconds = (int) WorkSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('started_at', $date)
            ->get()
            ->sum(fn (WorkSession $s) => $s->workedSeconds());

        if ($includeActive) {
            $active = $this->timer->activeSession($userId);
            if ($active && $active->started_at->toDateString() === $date) {
                $completedSeconds += $this->timer->tickElapsed($active);
            }
        }

        return $completedSeconds;
    }

    public function liveStatsForDate(int $userId, ?string $date = null): array
    {
        $date = $date ?? Carbon::today()->toDateString();
        $completedSeconds = $this->completedSecondsForDate($userId, $date);
        $completedHours = round($completedSeconds / 3600, 2);
        $target = (float) self::DAILY_TARGET_HOURS;
        $remaining = max(0, round($target - $completedHours, 2));
        $progress = min(100, (int) round(($completedHours / max(0.01, $target)) * 100));

        return [
            'date' => $date,
            'target_hours' => $target,
            'completed_hours' => $completedHours,
            'remaining_hours' => $remaining,
            'progress_percent' => $progress,
            'completed_seconds' => $completedSeconds,
        ];
    }

    public function recalculateForUser(int $userId, string $date): DailySummary
    {
        $stats = $this->liveStatsForDate($userId, $date);

        return DailySummary::updateOrCreate(
            ['user_id' => $userId, 'summary_date' => $date],
            [
                'target_hours' => $stats['target_hours'],
                'completed_hours' => $stats['completed_hours'],
                'remaining_hours' => $stats['remaining_hours'],
            ]
        );
    }

    public function getOrCreateToday(int $userId): DailySummary
    {
        return $this->recalculateForUser($userId, Carbon::today()->toDateString());
    }
}
