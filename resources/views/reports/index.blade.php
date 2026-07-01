@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<h1 class="text-2xl font-bold mb-6">Weekly Reports</h1>

<section class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 mb-8">
    <div class="max-w-2xl">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Generate Report</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 mb-4">Select a sprint to preview its weekly summary, category breakdown, work logs, and daily hours before exporting.</p>
        <form method="GET" action="{{ route('reports.preview') }}" class="space-y-3">
            <div>
                <label for="sprint_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sprint</label>
                <select name="sprint_id" id="sprint_id" required
                    class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— Choose a sprint —</option>
                    @foreach($sprints as $sprint)
                        <option value="{{ $sprint->id }}" @selected(request('sprint_id') == $sprint->id)>
                            {{ $sprint->name }}
                            ({{ $sprint->start_date->format('M d') }} – {{ $sprint->end_date->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium"
                @disabled($sprints->isEmpty())>
                Preview Report
            </button>
            @if($sprints->isEmpty())
                <p class="text-sm text-amber-600">Create a sprint first to generate reports.</p>
            @endif
        </form>
    </div>
</section>

<section class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 dark:bg-slate-900">
        <h2 class="font-semibold text-slate-800 dark:text-slate-200">Generated Reports</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b bg-slate-50 dark:bg-slate-900/50">
                    <th class="px-5 py-3 font-medium">Sprint</th>
                    <th class="px-5 py-3 font-medium">Period</th>
                    <th class="px-5 py-3 font-medium">Generated</th>
                    <th class="px-5 py-3 font-medium">Downloads</th>
                </tr>
            </thead>
            <tbody>
                @forelse($generated as $report)
                <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50 dark:bg-slate-900/50">
                    <td class="px-5 py-3">
                        <a href="{{ route('reports.show', $report) }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">{{ $report->sprint->name }}</a>
                    </td>
                    <td class="px-5 py-3 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $report->week_start->format('M d') }} – {{ $report->week_end->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $report->generated_at->format('M d, Y H:i') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('reports.download', [$report, 'pdf']) }}" class="text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 px-2.5 py-1 rounded">PDF</a>
                            <a href="{{ route('reports.download', [$report, 'excel']) }}" class="text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 px-2.5 py-1 rounded">Excel</a>
                            <a href="{{ route('reports.download', [$report, 'csv']) }}" class="text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 px-2.5 py-1 rounded">CSV</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No reports generated yet. Use Preview above, then export.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@if($generated->hasPages())
<div class="mt-4">{{ $generated->links() }}</div>
@endif
@endsection
