@extends('layouts.app')

@section('title', 'Report Preview')

@section('content')
<nav class="text-sm text-slate-500 dark:text-slate-400 mb-4">
    <a href="{{ route('reports.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Reports</a>
    <span class="mx-1">/</span>
    <span class="text-slate-700 dark:text-slate-300">Preview</span>
</nav>

<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Report Preview</h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">Review the sprint report below before exporting.</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:bg-slate-900">Back</a>
            <form method="POST" action="{{ route('reports.generate') }}">
                @csrf
                <input type="hidden" name="sprint_id" value="{{ $sprint->id }}">
                <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                    Generate & Export (PDF / Excel / CSV)
                </button>
            </form>
        </div>
    </div>
</div>

@include('reports._body', ['data' => $data])
@endsection
