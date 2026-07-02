<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class DashboardService
 *
 * Handles the business logic for generating dashboard analytics and statistics.
 * Follows the Single Responsibility Principle by delegating specific tasks
 * to the DailySummaryService and TimerService where appropriate.
 */
class DashboardService
{
    /**
     * DashboardService constructor.
     *
     * @param DailySummaryService $dailySummaryService Handles daily aggregate data.
     * @param TimerService $timerService Handles live timer calculations.
     */
    public function __construct(
        protected DailySummaryService $dailySummaryService,
        protected TimerService $timerService,
    ) {}

    /**
     * Retrieves the daily dashboard statistics for a given user.
     *
     * @param int $userId The ID of the user.
     * @return array Contains summary, liveStats, activeTimer, categoryBreakdown, and recentActivities.
     */
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

    /**
     * Calculates the breakdown of work hours per category for a specific date.
     *
     * @param int $userId The ID of the user.
     * @param string $date The date to analyze (Y-m-d).
     * @param WorkSession|null $activeTimer The currently running timer session, if any.
     * @return Collection Collection of category breakdowns (category name, color, and hours).
     */
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

    /**
     * Retrieves the sprint dashboard statistics for a given user.
     *
     * @param int|null $sprintId The ID of the sprint, or null to auto-select the latest active sprint.
     * @param int $userId The ID of the user.
     * @return array Contains sprint details, task lists, and chart data.
     */
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

    /**
     * Calculates the breakdown of completed work hours per category for a specific sprint.
     *
     * @param int $sprintId The ID of the sprint.
     * @return Collection Collection of category breakdowns.
     */
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

    /**
     * Retrieves all sprints for a user to be used in select dropdowns.
     *
     * @param int $userId The ID of the user.
     * @return Collection Collection of Sprint models.
     */
    public function sprintsForSelect(int $userId): Collection
    {
        return Sprint::where('user_id', $userId)->orderByDesc('start_date')->get();
    }

    /**
     * Retrieves only active (non-completed) sprints for the timer UI.
     *
     * @param int $userId The ID of the user.
     * @return Collection Collection of active Sprint models.
     */
    public function activeSprintsForTimer(int $userId): Collection
    {
        return Sprint::where('user_id', $userId)
            ->where('is_completed', false)
            ->orderByDesc('start_date')
            ->get();
    }

    /**
     * Retrieves all tasks associated with a specific sprint.
     *
     * @param int $sprintId The ID of the sprint.
     * @return Collection Collection of Task models.
     */
    public function tasksForSprint(int $sprintId): Collection
    {
        return Task::where('sprint_id', $sprintId)->orderBy('title')->get();
    }

    /**
     * Retrieves pending tasks for a sprint with their associated categories.
     * Used for auto-populating dropdowns in the timer UI.
     *
     * @param int $sprintId The ID of the sprint.
     * @return Collection Mapped collection of task arrays.
     */
    public function tasksForPendingSprint(int $sprintId): Collection
    {
        return Task::with('category')
            ->where('sprint_id', $sprintId)
            ->where('status', '!=', 'completed')
            ->orderBy('title')
            ->get()
            ->map(function (Task $t) {
                return [
                    'id'          => $t->id,
                    'title'       => $t->title,
                    'category_id' => $t->category_id,
                    'category'    => $t->category?->name,
                ];
            });
    }

    /**
     * Retrieves all available categories.
     *
     * @return Collection Collection of Category models.
     */
    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }
}

