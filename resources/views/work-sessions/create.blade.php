@extends('layouts.app')

@section('title', 'Log Past Session')

@section('content')
<h1 class="text-2xl font-bold mb-2">Log past work session</h1>
<p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Add time you forgot to track — set start/end for any previous day.</p>

<form method="POST" action="{{ route('work-sessions.store') }}" class="bg-white dark:bg-slate-800 rounded-xl border p-6 shadow-sm max-w-2xl space-y-4">
    @csrf
    @include('work-sessions._form', ['session' => null, 'tasks' => collect()])
    <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Save session</button>
        <a href="{{ route('work-sessions.index') }}" class="px-4 py-2 border rounded-lg text-sm">Cancel</a>
    </div>
</form>
@endsection
