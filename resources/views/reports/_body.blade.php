{{-- Sprint summary header --}}
<section class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="bg-indigo-600 px-5 py-4 text-white">
        <p class="text-indigo-200 text-xs font-medium uppercase tracking-wide">Sprint Summary</p>
        <h2 class="text-xl font-bold mt-1">{{ $data['sprint']['name'] }}</h2>
        <p class="text-indigo-100 text-sm mt-1">
            {{ $data['sprint']['start_date'] }} — {{ $data['sprint']['end_date'] }}
            @if($data['sprint']['is_completed'])
                <span class="ml-2 inline-block bg-indigo-500 text-white text-xs px-2 py-0.5 rounded">Completed</span>
            @else
                <span class="ml-2 inline-block bg-amber-400 text-amber-950 text-xs px-2 py-0.5 rounded">In Progress</span>
            @endif
        </p>
    </div>
    @if(!empty($data['sprint']['goal']) && trim(strip_tags($data['sprint']['goal'])) !== '')
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 dark:bg-slate-900">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Sprint Goal</p>
        @include('partials.sprint-goal', ['goal' => $data['sprint']['goal'], 'class' => 'text-sm text-slate-700 dark:text-slate-300'])
    </div>
    @endif
</section>

{{-- KPI cards --}}
<section class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Estimated Hours</p>
        <p class="text-2xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ number_format($data['summary']['estimated_hours'], 2) }}<span class="text-base font-normal text-slate-500 dark:text-slate-400">h</span></p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Actual Hours</p>
        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($data['summary']['actual_hours'], 2) }}<span class="text-base font-normal text-slate-500 dark:text-slate-400">h</span></p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tasks Done / Pending</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $data['summary']['completed_tasks'] }}<span class="text-slate-400 font-normal"> / </span><span class="text-amber-600">{{ $data['summary']['pending_tasks'] }}</span></p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Estimation Accuracy</p>
        <p class="text-2xl font-bold mt-1">
            @if($data['summary']['estimation_accuracy'] !== null)
                {{ $data['summary']['estimation_accuracy'] }}<span class="text-base">%</span>
            @else
                <span class="text-slate-400 text-lg">N/A</span>
            @endif
        </p>
    </div>
</section>

{{-- Breakdown tables --}}
<section class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 dark:bg-slate-900">
            <h3 class="font-semibold text-slate-800 dark:text-slate-200">Category Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            @if(count($data['category_breakdown']) > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 dark:text-slate-400 border-b">
                        <th class="px-5 py-3 font-medium">Category</th>
                        <th class="px-5 py-3 font-medium text-right">Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['category_breakdown'] as $row)
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50 dark:bg-slate-900/50">
                        <td class="px-5 py-3 text-slate-800 dark:text-slate-200">{{ $row['category'] }}</td>
                        <td class="px-5 py-3 text-right font-medium text-indigo-600 dark:text-indigo-400">{{ number_format($row['hours'], 2) }}h</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="px-5 py-8 text-center text-slate-500 dark:text-slate-400 text-sm">No completed work sessions by category yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 dark:bg-slate-900">
            <h3 class="font-semibold text-slate-800 dark:text-slate-200">Daily Hour Summary</h3>
        </div>
        <div class="overflow-x-auto">
            @if(count($data['daily_hours']) > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 dark:text-slate-400 border-b">
                        <th class="px-5 py-3 font-medium">Date</th>
                        <th class="px-5 py-3 font-medium text-right">Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['daily_hours'] as $day)
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50 dark:bg-slate-900/50">
                        <td class="px-5 py-3 text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($day['date'])->format('D, M d, Y') }}</td>
                        <td class="px-5 py-3 text-right font-medium text-indigo-600 dark:text-indigo-400">{{ number_format($day['hours'], 2) }}h</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="px-5 py-8 text-center text-slate-500 dark:text-slate-400 text-sm">No daily hours logged for this sprint yet.</p>
            @endif
        </div>
    </div>
</section>

{{-- Work logs --}}
<section class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 dark:bg-slate-900 flex flex-wrap items-center justify-between gap-2">
        <h3 class="font-semibold text-slate-800 dark:text-slate-200">Detailed Work Logs</h3>
        <span class="text-xs text-slate-500 dark:text-slate-400">{{ count($data['work_logs']) }} session(s)</span>
    </div>
    @if(count($data['work_logs']) > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b bg-slate-50 dark:bg-slate-900/80">
                    <th class="px-4 py-3 font-medium whitespace-nowrap">Date & Time</th>
                    <th class="px-4 py-3 font-medium">Task</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium text-right">Hours</th>
                    <th class="px-4 py-3 font-medium min-w-[200px]">Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['work_logs'] as $log)
                <tr class="border-b border-slate-50 last:border-0 align-top hover:bg-slate-50 dark:bg-slate-900/50">
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $log['date'] }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $log['task'] }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $log['category'] }}</td>
                    <td class="px-4 py-3 text-right font-medium text-indigo-600 dark:text-indigo-400 whitespace-nowrap">{{ number_format($log['hours'], 2) }}h</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400 break-words max-w-xs">{{ $log['description'] ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="px-5 py-10 text-center text-slate-500 dark:text-slate-400">No completed work sessions to include in this report. Complete timer sessions first.</p>
    @endif
</section>
