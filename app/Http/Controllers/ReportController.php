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

/**
 * Class ReportController
 *
 * Handles HTTP requests for viewing, generating, and downloading reports.
 */
class ReportController extends Controller
{
    /**
     * ReportController constructor.
     *
     * @param ReportService $reports Handles report generation logic.
     * @param DashboardService $dashboard Handles dashboard data retrieval.
     */
    public function __construct(
        protected ReportService $reports,
        protected DashboardService $dashboard,
    ) {}

    /**
     * Displays the weekly report generation interface and history.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $sprints = $this->dashboard->sprintsForSelect($request->user()->id);
        $generated = WeeklyReport::with('sprint')
            ->where('user_id', $request->user()->id)
            ->latest('generated_at')
            ->paginate(10);

        return view('reports.index', compact('sprints', 'generated'));
    }

    /**
     * Displays the monthly analytics dashboard.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function monthly(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $data = $this->reports->getMonthlyReportData($request->user()->id, $start, $end);
        
        return view('reports.monthly', array_merge(compact('startDate', 'endDate'), $data));
    }

    /**
     * Generates and downloads the monthly analytics report as a PDF.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function monthlyPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $data = $this->reports->getMonthlyReportData($request->user()->id, $start, $end);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        // Required by dompdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly_pdf', $data);

        return $pdf->download("monthly_report_{$startDate}_{$endDate}.pdf");
    }

    /**
     * Previews a weekly report before generation.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'sprint_id' => ['required', 'exists:sprints,id'],
        ]);

        $sprint = Sprint::where('user_id', $request->user()->id)->findOrFail($validated['sprint_id']);
        $data = $this->reports->buildReportData($sprint);

        return view('reports.preview', compact('data', 'sprint'));
    }

    /**
     * Generates and stores a weekly report for a specific sprint.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'sprint_id' => ['required', 'exists:sprints,id'],
        ]);

        $sprint = Sprint::where('user_id', $request->user()->id)->findOrFail($validated['sprint_id']);
        $report = $this->reports->generateAndStore($sprint, $request->user()->id);

        return redirect()->route('reports.show', $report)->with('success', 'Weekly report generated.');
    }

    /**
     * Displays a generated weekly report.
     *
     * @param WeeklyReport $weeklyReport
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(WeeklyReport $weeklyReport, Request $request)
    {
        abort_unless($weeklyReport->user_id === $request->user()->id, 403);

        return view('reports.show', [
            'report' => $weeklyReport->load('sprint'),
            'data' => $weeklyReport->report_data,
        ]);
    }

    /**
     * Downloads a generated weekly report in the specified format.
     *
     * @param WeeklyReport $weeklyReport
     * @param Request $request
     * @param string $format The format to download (pdf, excel, csv).
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
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
