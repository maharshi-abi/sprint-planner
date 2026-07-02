<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Models\WeeklyReport;
use App\Services\DashboardService;
use App\Services\ReportService;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reports,
        protected DashboardService $dashboard,
    ) {}

    public function index(Request $request)
    {
        $sprints = $this->dashboard->sprintsForSelect($request->user()->id);
        $generated = WeeklyReport::with('sprint')
            ->where('user_id', $request->user()->id)
            ->latest('generated_at')
            ->paginate(10);

        return view('reports.index', compact('sprints', 'generated'));
    }

    public function monthly(Request $request)
    {
        $data = $this->getMonthlyReportData($request);
        return view('reports.monthly', $data);
    }

    public function monthlyPdf(Request $request)
    {
        $data = $this->getMonthlyReportData($request);

        // Required by dompdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly_pdf', $data);

        return $pdf->download("monthly_report_{$data['startDate']}_{$data['endDate']}.pdf");
    }

    private function getMonthlyReportData(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $workSessions = WorkSession::with(['task', 'category', 'sprint'])
            ->where('user_id', $request->user()->id)
            ->whereBetween('started_at', [$start, $end])
            ->get();

        $taskStats = [];
        $dailyStats = [];

        foreach ($workSessions as $session) {
            $taskName = $session->task ? $session->task->title : 'Unassigned Task';
            if (!isset($taskStats[$taskName])) {
                $taskStats[$taskName] = 0;
            }
            $taskStats[$taskName] += $session->workedHours();

            $dateKey = $session->started_at->format('Y-m-d');
            if (!isset($dailyStats[$dateKey])) {
                $dailyStats[$dateKey] = 0;
            }
            $dailyStats[$dateKey] += $session->workedHours();
        }

        ksort($dailyStats);

        $completedTasks = \App\Models\Task::whereHas('sprint', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$start, $end])
            ->get();

        $dailyPoints = [];
        foreach ($completedTasks as $task) {
            $dateKey = $task->updated_at->format('Y-m-d');
            if (!isset($dailyPoints[$dateKey])) {
                $dailyPoints[$dateKey] = 0;
            }
            $dailyPoints[$dateKey] += $task->estimated_hours ?? 1;
        }
        ksort($dailyPoints);

        return compact('startDate', 'endDate', 'taskStats', 'dailyStats', 'dailyPoints', 'workSessions');
    }


    public function preview(Request $request)
    {
        $validated = $request->validate([
            'sprint_id' => ['required', 'exists:sprints,id'],
        ]);

        $sprint = Sprint::where('user_id', $request->user()->id)->findOrFail($validated['sprint_id']);
        $data = $this->reports->buildReportData($sprint);

        return view('reports.preview', compact('data', 'sprint'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'sprint_id' => ['required', 'exists:sprints,id'],
        ]);

        $sprint = Sprint::where('user_id', $request->user()->id)->findOrFail($validated['sprint_id']);
        $report = $this->reports->generateAndStore($sprint, $request->user()->id);

        return redirect()->route('reports.show', $report)->with('success', 'Weekly report generated.');
    }

    public function show(WeeklyReport $weeklyReport, Request $request)
    {
        abort_unless($weeklyReport->user_id === $request->user()->id, 403);

        return view('reports.show', [
            'report' => $weeklyReport->load('sprint'),
            'data' => $weeklyReport->report_data,
        ]);
    }

    public function download(WeeklyReport $weeklyReport, Request $request, string $format)
    {
        abort_unless($weeklyReport->user_id === $request->user()->id, 403);

        $path = match ($format) {
            'pdf' => $weeklyReport->pdf_path,
            'excel', 'xlsx' => $weeklyReport->excel_path,
            'csv' => $weeklyReport->csv_path,
            default => abort(404),
        };

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}
