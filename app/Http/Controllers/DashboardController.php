<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
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
}
