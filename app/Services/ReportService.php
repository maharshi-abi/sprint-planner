<?php

namespace App\Services;

use App\Models\Sprint;
use App\Support\SprintGoalFormatter;
use App\Models\WeeklyReport;
use App\Models\WorkSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class ReportService
 *
 * Handles the generation, structuring, and storage of weekly and monthly reports.
 */
class ReportService
{
    /**
     * Builds the structured data required for a weekly sprint report.
     *
     * @param Sprint $sprint The sprint to build the report for.
     * @return array The structured report data.
     */
    public function buildReportData(Sprint $sprint): array
    {
        $sessions = WorkSession::with(['task', 'category', 'interruptions'])
            ->where('sprint_id', $sprint->id)
            ->where('status', 'completed')
            ->orderBy('started_at')
            ->get();

        $categoryBreakdown = $sessions->groupBy('category_id')->map(function ($group) {
            $category = $group->first()->category;

            return [
                'category' => $category->name,
                'hours' => round($group->sum(fn ($s) => $s->workedSeconds()) / 3600, 2),
            ];
        })->values()->all();

        $dailyHours = $sessions->groupBy(fn ($s) => Carbon::parse($s->started_at)->toDateString())
            ->map(fn ($group, $date) => [
                'date' => $date,
                'hours' => round($group->sum(fn ($s) => $s->workedSeconds()) / 3600, 2),
            ])
            ->values()
            ->all();

        $workLogs = $sessions->map(fn ($s) => [
            'date' => $s->started_at->format('Y-m-d H:i'),
            'task' => $s->task->title,
            'category' => $s->category->name,
            'description' => $s->description,
            'hours' => $s->workedHours(),
            'interruptions' => $s->interruptions->count(),
        ])->all();

        return [
            'sprint' => [
                'name' => $sprint->name,
                'goal' => $sprint->goal,
                'start_date' => $sprint->start_date->format('Y-m-d'),
                'end_date' => $sprint->end_date->format('Y-m-d'),
                'is_completed' => $sprint->is_completed,
            ],
            'summary' => [
                'estimated_hours' => $sprint->totalEstimatedHours(),
                'actual_hours' => $sprint->totalActualHours(),
                'completed_tasks' => $sprint->tasks()->where('status', 'completed')->count(),
                'pending_tasks' => $sprint->tasks()->where('status', '!=', 'completed')->count(),
                'estimation_accuracy' => $sprint->estimationAccuracy(),
            ],
            'category_breakdown' => $categoryBreakdown,
            'daily_hours' => $dailyHours,
            'work_logs' => $workLogs,
        ];
    }

    /**
     * Generates the PDF, Excel, and CSV files for a weekly report and stores the record in the database.
     *
     * @param Sprint $sprint The sprint to generate the report for.
     * @param int $userId The ID of the user generating the report.
     * @return WeeklyReport The created WeeklyReport model instance.
     */
    public function generateAndStore(Sprint $sprint, int $userId): WeeklyReport
    {
        $data = $this->buildReportData($sprint);
        $dir = "reports/sprint-{$sprint->id}";
        Storage::disk('local')->makeDirectory($dir);

        $base = $dir.'/report-'.now()->format('YmdHis');
        $pdfPath = $base.'.pdf';
        $excelPath = $base.'.xlsx';
        $csvPath = $base.'.csv';

        Pdf::loadView('reports.pdf', ['data' => $data])->save(storage_path('app/private/'.$pdfPath));
        WeeklyReportSpreadsheet::save($data, storage_path('app/private/'.$excelPath));
        $this->storeCsv($data, $csvPath);

        return WeeklyReport::create([
            'user_id' => $userId,
            'sprint_id' => $sprint->id,
            'week_start' => $sprint->start_date,
            'week_end' => $sprint->end_date,
            'report_data' => $data,
            'pdf_path' => $pdfPath,
            'excel_path' => $excelPath,
            'csv_path' => $csvPath,
            'generated_at' => now(),
        ]);
    }

    protected function storeCsv(array $data, string $path): void
    {
        $handle = fopen(storage_path('app/private/'.$path), 'w');
        fputcsv($handle, ['Sprint Weekly Report']);
        fputcsv($handle, ['Sprint', $data['sprint']['name']]);
        fputcsv($handle, ['Goal', SprintGoalFormatter::plainText($data['sprint']['goal'])]);
        fputcsv($handle, []);
        fputcsv($handle, ['Category', 'Hours']);
        foreach ($data['category_breakdown'] as $row) {
            fputcsv($handle, [$row['category'], $row['hours']]);
        }
        fputcsv($handle, []);
        fputcsv($handle, ['Date', 'Task', 'Category', 'Hours', 'Description']);
        foreach ($data['work_logs'] as $log) {
            fputcsv($handle, [$log['date'], $log['task'], $log['category'], $log['hours'], $log['description']]);
        }
        fclose($handle);
    }

    /**
     * Aggregates work sessions and task points for a monthly report.
     *
     * @param int $userId The ID of the user.
     * @param Carbon $start The start date of the reporting period.
     * @param Carbon $end The end date of the reporting period.
     * @return array Contains taskStats, dailyStats, dailyPoints, and workSessions.
     */
    public function getMonthlyReportData(int $userId, Carbon $start, Carbon $end): array
    {
        $workSessions = WorkSession::with(['task', 'category', 'sprint'])
            ->where('user_id', $userId)
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

        $completedTasks = \App\Models\Task::whereHas('sprint', function ($q) use ($userId) {
                $q->where('user_id', $userId);
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

        return [
            'taskStats' => $taskStats,
            'dailyStats' => $dailyStats,
            'dailyPoints' => $dailyPoints,
            'workSessions' => $workSessions,
        ];
    }
}
