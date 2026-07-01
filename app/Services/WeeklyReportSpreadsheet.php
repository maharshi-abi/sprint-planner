<?php

namespace App\Services;

use App\Support\SprintGoalFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WeeklyReportSpreadsheet
{
    public static function rows(array $data): array
    {
        $rows = [
            ['Sprint Weekly Report'],
            ['Sprint', $data['sprint']['name']],
            ['Goal', SprintGoalFormatter::plainText($data['sprint']['goal'])],
            ['Period', $data['sprint']['start_date'].' to '.$data['sprint']['end_date']],
            [],
            ['Estimated Hours', $data['summary']['estimated_hours']],
            ['Actual Hours', $data['summary']['actual_hours']],
            ['Estimation Accuracy %', $data['summary']['estimation_accuracy']],
            [],
            ['Category Breakdown'],
            ['Category', 'Hours'],
        ];

        foreach ($data['category_breakdown'] as $row) {
            $rows[] = [$row['category'], $row['hours']];
        }

        $rows[] = [];
        $rows[] = ['Daily Hour Summary'];
        $rows[] = ['Date', 'Hours'];
        foreach ($data['daily_hours'] as $day) {
            $rows[] = [$day['date'], $day['hours']];
        }

        $rows[] = [];
        $rows[] = ['Work Logs'];
        $rows[] = ['Date', 'Task', 'Category', 'Hours', 'Description', 'Interruptions'];
        foreach ($data['work_logs'] as $log) {
            $rows[] = [
                $log['date'],
                $log['task'],
                $log['category'],
                $log['hours'],
                $log['description'],
                $log['interruptions'],
            ];
        }

        return $rows;
    }

    public static function save(array $data, string $absolutePath): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Weekly Report');
        $sheet->fromArray(self::rows($data), null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $writer->save($absolutePath);
    }
}
