@extends('layouts.app')

@section('title', 'Report')

@section('content')
<nav class="text-sm text-slate-500 dark:text-slate-400 mb-4">
    <a href="{{ route('reports.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Reports</a>
    <span class="mx-1">/</span>
    <span class="text-slate-700 dark:text-slate-300">{{ $report->sprint->name }}</span>
</nav>

<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $report->sprint->name }}</h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">Generated {{ $report->generated_at->format('M d, Y \a\t H:i') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reports.download', [$report, 'pdf']) }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:bg-slate-900">PDF</a>
            <a href="{{ route('reports.download', [$report, 'excel']) }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:bg-slate-900">Excel</a>
            <a href="{{ route('reports.download', [$report, 'csv']) }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:bg-slate-900">CSV</a>
        </div>
    </div>
</div>

@include('reports._body', ['data' => $data])
@endsection
