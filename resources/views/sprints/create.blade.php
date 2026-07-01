@extends('layouts.app')

@section('title', 'New Sprint')

@section('content')
<h1 class="text-2xl font-bold mb-6">Create Weekly Sprint</h1>
<form method="POST" action="{{ route('sprints.store') }}" class="bg-white dark:bg-slate-800 rounded-xl border p-6 shadow-sm max-w-2xl space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Sprint Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2">
    </div>
    @include('partials.sprint-goal-editor', ['goal' => old('goal')])
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', now()->startOfWeek()->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date', now()->endOfWeek()->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
    </div>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Create Sprint</button>
</form>
@endsection
