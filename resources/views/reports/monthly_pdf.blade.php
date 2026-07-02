<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Analytics Report ({{ $startDate }} to {{ $endDate }})</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #334155; margin: 0; padding: 0; }
        h1 { color: #1e293b; font-size: 18px; margin-bottom: 2px; }
        h2 { color: #334155; font-size: 14px; margin-top: 24px; margin-bottom: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .subtitle { font-size: 12px; color: #64748b; margin-bottom: 16px; }

        /* KPIs Grid */
        .kpi-container { width: 100%; margin-bottom: 24px; }
        .kpi-box {
            width: 23%;
            display: inline-block;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            box-sizing: border-box;
            vertical-align: top;
            margin-right: 1.5%;
        }
        .kpi-box.last { margin-right: 0; }
        .kpi-label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .kpi-value { font-size: 20px; font-weight: bold; color: #4f46e5; margin-top: 6px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-weight: bold; color: #475569; }
        td { color: #334155; }
        tr:nth-child(even) { background-color: #f8fafc; }

        /* Progress Bar in Task Table */
        .bar-bg { width: 100%; background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden; display: inline-block; }
        .bar-fg { background: #6366f1; height: 100%; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Page Breaks */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    @php
        $totalHours  = array_sum($dailyStats);
        $totalPoints = array_sum($dailyPoints);
        $workDays    = count(array_filter($dailyStats, fn($h) => $h > 0));
        $avgHoursDay = $workDays > 0 ? round($totalHours / $workDays, 2) : 0;
        arsort($taskStats);
    @endphp

    <h1>Monthly Analytics & Performance Report</h1>
    <div class="subtitle">{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</div>

    <div class="kpi-container">
        <div class="kpi-box">
            <div class="kpi-label">Total Hours</div>
            <div class="kpi-value">{{ number_format($totalHours, 1) }}h</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Avg Hours/Day</div>
            <div class="kpi-value" style="color: #10b981;">{{ $avgHoursDay }}h</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Points Done</div>
            <div class="kpi-value" style="color: #8b5cf6;">{{ number_format($totalPoints, 1) }}</div>
        </div>
        <div class="kpi-box last">
            <div class="kpi-label">Tasks Tracked</div>
            <div class="kpi-value" style="color: #d97706;">{{ count($taskStats) }}</div>
        </div>
    </div>

    <h2>Task Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Task</th>
                <th style="width: 25%;">Share</th>
                <th style="width: 15%; text-align: right;">Hours</th>
                <th style="width: 10%; text-align: right;">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse($taskStats as $task => $hours)
            @php $share = $totalHours > 0 ? round($hours / $totalHours * 100) : 0; @endphp
            <tr>
                <td>{{ $task }}</td>
                <td>
                    <div class="bar-bg">
                        <div class="bar-fg" style="width: {{ $share }}%"></div>
                    </div>
                </td>
                <td class="text-right">{{ number_format($hours, 2) }}h</td>
                <td class="text-right">{{ $share }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No tasks worked on in this period.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($taskStats) > 0)
        <tfoot>
            <tr>
                <td colspan="2" style="font-weight: bold; text-align: right; border-top: 2px solid #cbd5e1;">Total:</td>
                <td class="text-right" style="font-weight: bold; border-top: 2px solid #cbd5e1;">{{ number_format($totalHours, 2) }}h</td>
                <td class="text-right" style="font-weight: bold; border-top: 2px solid #cbd5e1;">100%</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="page-break"></div>

    <h2>Daily Performance Log</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-right">Hours Worked</th>
                <th class="text-right">Points Completed</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Merge keys to get a continuous list of dates where activity occurred
                $dates = array_unique(array_merge(array_keys($dailyStats), array_keys($dailyPoints)));
                rsort($dates); // show most recent first
            @endphp
            @forelse($dates as $date)
            <tr>
                <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y (l)') }}</td>
                <td class="text-right">{{ isset($dailyStats[$date]) ? number_format($dailyStats[$date], 2) . 'h' : '0.00h' }}</td>
                <td class="text-right">{{ isset($dailyPoints[$date]) ? $dailyPoints[$date] : 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No daily activity recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Detailed Work Log</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Date & Time</th>
                <th style="width: 25%;">Task</th>
                <th style="width: 15%;">Category</th>
                <th style="width: 10%; text-align: right;">Hours</th>
                <th style="width: 35%;">Notes / Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workSessions as $session)
            <tr>
                <td>{{ $session->started_at->format('M d, H:i') }}</td>
                <td>{{ $session->task?->title ?? '—' }}</td>
                <td>{{ $session->category?->name ?? '—' }}</td>
                <td class="text-right">
                    @if($session->status === 'completed')
                        {{ $session->workedHours() }}h
                    @else
                        <em>{{ $session->status }}</em>
                    @endif
                </td>
                <td style="font-size: 10px; color: #475569;">
                    @if($session->description)
                        {{ $session->description }}
                    @else
                        <em>No description</em>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No work sessions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
