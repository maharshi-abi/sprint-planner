@extends('layouts.app')

@section('title', 'Weekly Reports')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-bold">Weekly Reports</h1>
    <a href="{{ route('reports.monthly') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Monthly Analytics →
    </a>
</div>

{{-- Generate Report Card --}}
<section class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 mb-6">
    <div class="max-w-2xl">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Generate Sprint Report</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 mb-4">Select a sprint to preview its weekly summary, category breakdown, work logs, and daily hours before exporting.</p>
        <form method="GET" action="{{ route('reports.preview') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
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
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2"
                @disabled($sprints->isEmpty())>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview Report
            </button>
            @if($sprints->isEmpty())
                <p class="text-sm text-amber-600">Create a sprint first to generate reports.</p>
            @endif
        </form>
    </div>
</section>

{{-- Quick KPI row --}}
@php
    $totalReports   = $generated->total();
    $latestReport   = $generated->first();
@endphp
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Reports Generated</p>
        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ $totalReports }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Sprints Covered</p>
        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">{{ $sprints->count() }}</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm col-span-2 sm:col-span-1">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Last Generated</p>
        <p class="text-lg font-bold text-slate-700 dark:text-slate-300 mt-2">
            {{ $latestReport ? $latestReport->generated_at->format('M d, Y') : '—' }}
        </p>
    </div>
</div>

{{-- Generated Reports Table --}}
<section class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <h2 class="font-semibold text-slate-800 dark:text-slate-200">Generated Reports</h2>
        <span class="text-xs text-slate-400 dark:text-slate-500">{{ $totalReports }} total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <th class="px-5 py-3 font-semibold">Sprint</th>
                    <th class="px-5 py-3 font-semibold">Period</th>
                    <th class="px-5 py-3 font-semibold">Generated</th>
                    <th class="px-5 py-3 font-semibold text-right">Downloads</th>
                </tr>
            </thead>
            <tbody>
                @forelse($generated as $report)
                <tr class="border-b border-slate-50 dark:border-slate-700/50 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('reports.show', $report) }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">{{ $report->sprint->name }}</a>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $report->week_start->format('M d') }} – {{ $report->week_end->format('M d, Y') }}</td>
                    <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $report->generated_at->format('M d, Y H:i') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex flex-wrap gap-2 justify-end">
                            <a href="{{ route('reports.download', [$report, 'pdf']) }}" class="text-xs bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-400 px-2.5 py-1 rounded-lg font-medium transition-colors">PDF</a>
                            <a href="{{ route('reports.download', [$report, 'excel']) }}" class="text-xs bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 px-2.5 py-1 rounded-lg font-medium transition-colors">Excel</a>
                            <a href="{{ route('reports.download', [$report, 'csv']) }}" class="text-xs bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 px-2.5 py-1 rounded-lg font-medium transition-colors">CSV</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        No reports generated yet. Use Preview above, then export.
                    </td>
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
