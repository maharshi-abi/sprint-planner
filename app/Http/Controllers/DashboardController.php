<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Models\Task;
use App\Models\WorkSession;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboard) {}

    public function daily(Request $request)
    {
        $data = $this->dashboard->daily($request->user()->id);

        return view('dashboard.daily', $data);
    }

    public function sprint(Request $request)
    {
        $data = $this->dashboard->sprint(
            $request->integer('sprint_id') ?: null,
            $request->user()->id
        );
        $data['sprints'] = $this->dashboard->sprintsForSelect($request->user()->id);

        return view('dashboard.sprint', $data);
    }

    public function dailyStats(Request $request)
    {
        return response()->json(
            app(\App\Services\DailySummaryService::class)->liveStatsForDate($request->user()->id)
        );
    }

    public function main(Request $request)
    {
        $userId = $request->user()->id;
        $data   = $this->buildMainStats($userId);

        return view('dashboard.main', $data);
    }

    public function mainStats(Request $request)
    {
        return response()->json($this->buildMainStats($request->user()->id));
    }

    protected function buildMainStats(int $userId): array
    {
        // Sprints
        $sprints        = Sprint::where('user_id', $userId)->get();
        $activeSprints  = $sprints->where('is_completed', false)->count();
        $totalSprints   = $sprints->count();

        // Tasks
        $allTasks        = Task::whereHas('sprint', fn($q) => $q->where('user_id', $userId))->get();
        $totalTasks      = $allTasks->count();
        $completedTasks  = $allTasks->where('status', 'completed')->count();
        $inProgressTasks = $allTasks->where('status', 'in_progress')->count();
        $pendingTasks    = $allTasks->where('status', 'pending')->count();

        // Work sessions
        $allSessions    = WorkSession::where('user_id', $userId)->get();
        $totalSessions  = $allSessions->count();
        $totalHours     = round($allSessions->where('status', 'completed')->sum(fn($s) => $s->workedHours()), 2);

        // Today
        $todaySessions  = $allSessions->filter(fn($s) => $s->started_at && $s->started_at->isToday());
        $todayHours     = round($todaySessions->where('status', 'completed')->sum(fn($s) => $s->workedHours()), 2);

        // This month
        $monthSessions  = WorkSession::where('user_id', $userId)
                            ->whereBetween('started_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->where('status', 'completed')
                            ->get();
        $monthHours     = round($monthSessions->sum(fn($s) => $s->workedHours()), 2);

        // Active timer
        $activeSession  = WorkSession::with(['task', 'category', 'sprint'])
                            ->where('user_id', $userId)
                            ->whereIn('status', ['active', 'paused'])
                            ->latest('id')->first();

        // Recent sessions (last 5)
        $recentSessions = WorkSession::with(['task', 'category', 'sprint'])
                            ->where('user_id', $userId)
                            ->latest('started_at')
                            ->limit(5)
                            ->get();

        // Daily hours last 7 days for chart
        $weeklyChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $hrs  = WorkSession::where('user_id', $userId)
                        ->whereDate('started_at', $date)
                        ->where('status', 'completed')
                        ->get()
                        ->sum(fn($s) => $s->workedHours());
            $weeklyChartData[$date->format('D')] = round($hrs, 2);
        }

        // Category breakdown (all time)
        $categoryBreakdown = WorkSession::where('user_id', $userId)
                                ->where('status', 'completed')
                                ->with('category')
                                ->get()
                                ->groupBy('category_id')
                                ->map(function ($sessions) {
                                    $cat = $sessions->first()->category;
                                    return [
                                        'category' => $cat?->name ?? 'Unknown',
                                        'color'    => $cat?->color ?? '#94a3b8',
                                        'hours'    => round($sessions->sum(fn($s) => $s->workedHours()), 2),
                                    ];
                                })->values();

        // Upcoming sprints / tasks count per sprint
        $sprintSummaries = Sprint::where('user_id', $userId)
                            ->where('is_completed', false)
                            ->with('tasks')
                            ->orderByDesc('start_date')
                            ->limit(5)
                            ->get()
                            ->map(function (Sprint $s) {
                                return [
                                    'id'         => $s->id,
                                    'name'       => $s->name,
                                    'start_date' => $s->start_date?->format('M d'),
                                    'end_date'   => $s->end_date?->format('M d, Y'),
                                    'total'      => $s->tasks->count(),
                                    'completed'  => $s->tasks->where('status', 'completed')->count(),
                                    'progress'   => $s->tasks->count() > 0
                                                    ? round($s->tasks->where('status', 'completed')->count() / $s->tasks->count() * 100)
                                                    : 0,
                                ];
                            });

        return compact(
            'activeSprints', 'totalSprints', 'totalTasks', 'completedTasks',
            'inProgressTasks', 'pendingTasks', 'totalSessions', 'totalHours',
            'todayHours', 'monthHours', 'activeSession', 'recentSessions',
            'weeklyChartData', 'categoryBreakdown', 'sprintSummaries'
        );
    }
}
