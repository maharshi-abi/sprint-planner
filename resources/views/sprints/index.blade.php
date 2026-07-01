@extends('layouts.app')

@section('title', 'Sprints')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Sprints</h1>
    <a href="{{ route('sprints.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">New Sprint</a>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-900 text-left">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Period</th>
                <th class="px-4 py-3">Tasks</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($sprints as $sprint)
            <tr class="border-t">
                <td class="px-4 py-3 font-medium">{{ $sprint->name }}</td>
                <td class="px-4 py-3">{{ $sprint->start_date->format('M d') }} – {{ $sprint->end_date->format('M d, Y') }}</td>
                <td class="px-4 py-3">{{ $sprint->tasks_count }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs {{ $sprint->is_completed ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $sprint->is_completed ? 'Completed' : 'Active' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('sprints.show', $sprint) }}" class="text-indigo-600 dark:text-indigo-400">View</a>
                    <a href="{{ route('sprints.edit', $sprint) }}" class="text-slate-600 dark:text-slate-400">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No sprints yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sprints->links() }}</div>
@endsection
