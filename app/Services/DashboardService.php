<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardService
{
    public function __construct(
        protected DailySummaryService $dailySummaryService,
        protected TimerService $timerService,
    ) {}

    public function daily(int $userId): array
    {
        $today = Carbon::today()->toDateString();
        $liveStats = $this->dailySummaryService->liveStatsForDate($userId, $today);
        $summary = $this->dailySummaryService->getOrCreateToday($userId);
        $summary->completed_hours = $liveStats['completed_hours'];
        $summary->remaining_hours = $liveStats['remaining_hours'];
        $activeTimer = $this->timerService->activeSession($userId);

        $categoryBreakdown = $this->categoryBreakdownForDate($userId, $today, $activeTimer);

        $recentActivities = WorkSession::with(['sprint', 'task', 'category'])
            ->where('user_id', $userId)
            ->latest('started_at')
            ->limit(10)
            ->get();

        return compact('summary', 'liveStats', 'activeTimer', 'categoryBreakdown', 'recentActivities');
    }

    protected function categoryBreakdownForDate(int $userId, string $date, ?WorkSession $activeTimer): Collection
    {
        $sessions = WorkSession::query()
            ->where('user_id', $userId)
            ->whereDate('started_at', $date)
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhereIn('status', ['active', 'paused']);
            })
            ->with('category')
            ->get();

        $breakdown = $sessions->groupBy('category_id')->map(function (Collection $group) use ($activeTimer) {
            $category = $group->first()->category;
            $seconds = $group->sum(function (WorkSession $s) use ($activeTimer) {
                if ($activeTimer && $s->id === $activeTimer->id) {
                    return $this->timerService->tickElapsed($s);
                }

                return $s->status === 'completed' ? $s->workedSeconds() : 0;
            });

            return [
                'category' => $category?->name ?? 'Unknown',
                'color' => $category?->color ?? '#94a3b8',
                'hours' => round($seconds / 3600, 2),
            ];
        })->values();

        return $breakdown->filter(fn($row) => $row['hours'] > 0)->values();
    }

    public function sprint(?int $sprintId, int $userId): array
    {
        $sprint = $sprintId
            ? Sprint::with('tasks.category')->where('user_id', $userId)->findOrFail($sprintId)
            : Sprint::with('tasks.category')->where('user_id', $userId)->where('is_completed', false)->latest('start_date')->first();

        if (! $sprint) {
            return ['sprint' => null];
        }

        $tasks = $sprint->tasks;
        $completedTasks = $tasks->where('status', 'completed');
        $pendingTasks = $tasks->where('status', '!=', 'completed');

        return [
            'sprint' => $sprint,
            'totalEstimated' => $sprint->totalEstimatedHours(),
            'totalActual' => $sprint->totalActualHours(),
            'completedCount' => $completedTasks->count(),
            'pendingCount' => $pendingTasks->count(),
            'estimationAccuracy' => $sprint->estimationAccuracy(),
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'categoryChart' => $this->sprintCategoryHours($sprint->id),
            'taskChart' => $tasks->map(fn(Task $t) => [
                'title' => Str::limit($t->title, 20),
                'estimated' => (float) $t->estimated_hours,
                'actual' => (float) $t->actual_hours,
            ])->values(),
        ];
    }

    protected function sprintCategoryHours(int $sprintId): Collection
    {
        return WorkSession::query()
            ->where('sprint_id', $sprintId)
            ->where('status', 'completed')
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function (Collection $sessions) {
                $category = $sessions->first()->category;

                return [
                    'label' => $category?->name ?? 'Unknown',
                    'hours' => round($sessions->sum(fn($s) => $s->workedSeconds()) / 3600, 2),
                    'color' => $category?->color ?? '#94a3b8',
                ];
            })
            ->values();
    }

    public function sprintsForSelect(int $userId): Collection
    {
        return Sprint::where('user_id', $userId)->orderByDesc('start_date')->get();
    }

    public function tasksForSprint(int $sprintId): Collection
    {
        return Task::where('sprint_id', $sprintId)->orderBy('title')->get();
    }

    public function tasksForPendingSprint(int $sprintId): Collection
    {
        return Task::where('sprint_id', $sprintId)->where('status', '!=', 'completed')->orderBy('title')->get();
    }


    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }
}
