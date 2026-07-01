@extends('layouts.app')

@section('title', 'Edit Work Session')

@section('content')
<h1 class="text-2xl font-bold mb-2">Edit work session</h1>
<p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Change date, time, task, or interruption minutes. Daily and sprint totals update automatically.</p>

<form method="POST" action="{{ route('work-sessions.update', $session) }}" class="bg-white dark:bg-slate-800 rounded-xl border p-6 shadow-sm max-w-2xl space-y-4">
    @csrf @method('PUT')
    @include('work-sessions._form', ['session' => $session, 'tasks' => $tasks])
    <div class="flex flex-wrap gap-3 pt-2">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Update session</button>
        <a href="{{ route('work-sessions.index') }}" class="px-4 py-2 border rounded-lg text-sm">Cancel</a>
    </div>
</form>
@endsection
