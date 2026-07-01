@extends('layouts.app')

@section('title', 'Edit Sprint')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Sprint</h1>
<form method="POST" action="{{ route('sprints.update', $sprint) }}" class="bg-white dark:bg-slate-800 rounded-xl border p-6 shadow-sm max-w-2xl space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Sprint Name</label>
        <input type="text" name="name" value="{{ old('name', $sprint->name) }}" required class="w-full border rounded-lg px-3 py-2">
    </div>
    @include('partials.sprint-goal-editor', ['goal' => old('goal', $sprint->goal)])
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', $sprint->start_date->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date', $sprint->end_date->format('Y-m-d')) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_completed" value="1" @checked(old('is_completed', $sprint->is_completed)) class="rounded">
        <span class="text-sm">Mark sprint as completed</span>
    </label>
    <div class="flex gap-3">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Save</button>
        <a href="{{ route('sprints.show', $sprint) }}" class="px-4 py-2 border rounded-lg text-sm">Cancel</a>
    </div>
</form>
@endsection
