@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Task</h1>
@include('tasks._form', ['action' => route('tasks.update', $task), 'task' => $task, 'method' => 'PUT'])
@endsection
