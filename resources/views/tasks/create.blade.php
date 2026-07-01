@extends('layouts.app')

@section('title', 'Add Task')

@section('content')
<h1 class="text-2xl font-bold mb-2">Add Task</h1>
<p class="text-slate-600 dark:text-slate-400 mb-6 text-sm">Sprint: {{ $sprint->name }}</p>
@include('tasks._form', ['action' => route('tasks.store', $sprint), 'task' => null])
@endsection
