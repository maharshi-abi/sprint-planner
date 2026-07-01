@extends('layouts.app')

@section('title', 'Work Log')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Work Log</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">View, edit, or add completed time for any day (backtrack missed entries).</p>
    </div>
    <a href="{{ route('work-sessions.create') }}" class="shrink-0 inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
        + Log past session
    </a>
</div>

<form method="GET" class="bg-white dark:bg-slate-800 rounded-xl border p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Date</label>
        <input type="date" name="date" value="{{ $filterDate }}" class="border rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Sprint</label>
        <select name="sprint_id" class="border rounded-lg px-3 py-2 text-sm min-w-[180px]">
            <option value="">All sprints</option>
            @foreach($sprints as $sprint)
                <option value="{{ $sprint->id }}" @selected($filterSprintId == $sprint->id)>{{ $sprint->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
    <a href="{{ route('work-sessions.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:underline py-2">Clear</a>
</form>

<div class="bg-white dark:bg-slate-800 rounded-xl border shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[720px]">
            <thead class="bg-slate-50 dark:bg-slate-900 text-left text-slate-600 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3 font-medium">Start</th>
                    <th class="px-4 py-3 font-medium">End</th>
                    <th class="px-4 py-3 font-medium">Worked</th>
                    <th class="px-4 py-3 font-medium">Sprint / Task</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr class="border-t hover:bg-slate-50 dark:bg-slate-900/50 align-top">
                    <td class="px-4 py-3 whitespace-nowrap">{{ $session->started_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $session->ended_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium text-indigo-600 dark:text-indigo-400">
                        {{ $session->workedHours() }}h
                        @if($session->interruption_seconds > 0)
                            <span class="block text-xs text-slate-500 dark:text-slate-400 font-normal">{{ (int) round($session->interruption_seconds / 60) }}m interrupted</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $session->task->title }}</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $session->sprint->name }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $session->category->name }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                        <a href="{{ route('work-sessions.edit', $session) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('work-sessions.destroy', $session) }}" class="inline"
                            onsubmit="return confirm('Delete this work session? Task hours and daily totals will be updated.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                        No completed sessions found.
                        <a href="{{ route('work-sessions.create') }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Log a past session</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($sessions->hasPages())
<div class="mt-4">{{ $sessions->links() }}</div>
@endif
@endsection
