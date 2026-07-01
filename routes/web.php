<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimerController;
use App\Http\Controllers\WorkSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', fn () => redirect()->route('dashboard.daily'))->name('dashboard');
    Route::get('/dashboard/daily', [DashboardController::class, 'daily'])->name('dashboard.daily');
    Route::get('/dashboard/daily/stats', [DashboardController::class, 'dailyStats'])->name('dashboard.daily.stats');
    Route::get('/dashboard/sprint', [DashboardController::class, 'sprint'])->name('dashboard.sprint');

    Route::resource('sprints', SprintController::class);

    Route::get('/sprints/{sprint}/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/sprints/{sprint}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/timer', [TimerController::class, 'index'])->name('timer.index');
    Route::get('/timer/tasks', [TimerController::class, 'tasks'])->name('timer.tasks');
    Route::post('/timer/start', [TimerController::class, 'start'])->name('timer.start');
    Route::post('/timer/{workSession}/pause', [TimerController::class, 'pause'])->name('timer.pause');
    Route::post('/timer/{workSession}/resume', [TimerController::class, 'resume'])->name('timer.resume');
    Route::post('/timer/{workSession}/stop', [TimerController::class, 'stop'])->name('timer.stop');
    Route::get('/timer/status', [TimerController::class, 'status'])->name('timer.status');

    Route::get('/work-sessions', [WorkSessionController::class, 'index'])->name('work-sessions.index');
    Route::get('/work-sessions/create', [WorkSessionController::class, 'create'])->name('work-sessions.create');
    Route::post('/work-sessions', [WorkSessionController::class, 'store'])->name('work-sessions.store');
    Route::get('/work-sessions/{workSession}/edit', [WorkSessionController::class, 'edit'])->name('work-sessions.edit');
    Route::put('/work-sessions/{workSession}', [WorkSessionController::class, 'update'])->name('work-sessions.update');
    Route::delete('/work-sessions/{workSession}', [WorkSessionController::class, 'destroy'])->name('work-sessions.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/preview', [ReportController::class, 'preview'])->name('reports.preview');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/{weeklyReport}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{weeklyReport}/download/{format}', [ReportController::class, 'download'])->name('reports.download');
});
