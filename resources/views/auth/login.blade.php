@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto mt-16">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8">
        <h1 class="text-2xl font-bold text-indigo-700 dark:text-indigo-300 mb-2">Developer Sprint Tracker</h1>
        <p class="text-slate-600 dark:text-slate-400 mb-6 text-sm">Sign in to manage sprints, timers, and weekly reports.</p>
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', 'developer@example.com') }}" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
                <input type="password" name="password" value="password" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border px-3 py-2">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-400">
                Remember me
            </label>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg">
                Sign in
            </button>
        </form>
        <p class="mt-4 text-xs text-slate-500 dark:text-slate-400 text-center">Default: developer@example.com / password</p>
    </div>
</div>
@endsection
