<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Report — {{ $data['sprint']['name'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h1 { color: #4338ca; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; }
        .summary { margin: 16px 0; }
        .summary span { display: inline-block; margin-right: 24px; }
        .sprint-goal-html { margin: 8px 0 12px; line-height: 1.5; }
        .sprint-goal-html p { margin: 4px 0; }
        .sprint-goal-html ul, .sprint-goal-html ol { margin: 4px 0; padding-left: 18px; }
        .sprint-goal-html h1, .sprint-goal-html h2, .sprint-goal-html h3 { margin: 6px 0 4px; font-weight: bold; }
        .sprint-goal-html a { color: #4338ca; }
    </style>
</head>
<body>
    <h1>Sprint Weekly Report</h1>
    <p><strong>{{ $data['sprint']['name'] }}</strong></p>
    @include('partials.sprint-goal', ['goal' => $data['sprint']['goal']])
    <p>{{ $data['sprint']['start_date'] }} to {{ $data['sprint']['end_date'] }}</p>

    <div class="summary">
        <span><strong>Estimated:</strong> {{ $data['summary']['estimated_hours'] }}h</span>
        <span><strong>Actual:</strong> {{ $data['summary']['actual_hours'] }}h</span>
        <span><strong>Accuracy:</strong> {{ $data['summary']['estimation_accuracy'] ?? 'N/A' }}%</span>
    </div>

    <h2>Category Breakdown</h2>
    <table>
        <tr><th>Category</th><th>Hours</th></tr>
        @foreach($data['category_breakdown'] as $row)
        <tr><td>{{ $row['category'] }}</td><td>{{ $row['hours'] }}</td></tr>
        @endforeach
    </table>

    <h2>Daily Hours</h2>
    <table>
        <tr><th>Date</th><th>Hours</th></tr>
        @foreach($data['daily_hours'] as $day)
        <tr><td>{{ $day['date'] }}</td><td>{{ $day['hours'] }}</td></tr>
        @endforeach
    </table>

    <h2>Work Logs</h2>
    <table>
        <tr><th>Date</th><th>Task</th><th>Category</th><th>Hours</th><th>Description</th></tr>
        @foreach($data['work_logs'] as $log)
        <tr>
            <td>{{ $log['date'] }}</td>
            <td>{{ $log['task'] }}</td>
            <td>{{ $log['category'] }}</td>
            <td>{{ $log['hours'] }}</td>
            <td>{{ $log['description'] }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
