<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sprint extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'goal',
        'start_date',
        'end_date',
        'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function workSessions(): HasMany
    {
        return $this->hasMany(WorkSession::class);
    }

    public function weeklyReports(): HasMany
    {
        return $this->hasMany(WeeklyReport::class);
    }

    public function totalEstimatedHours(): float
    {
        return (float) $this->tasks()->sum('estimated_hours');
    }

    public function totalActualHours(): float
    {
        return (float) $this->tasks()->sum('actual_hours');
    }

    public function estimationAccuracy(): ?float
    {
        $estimated = $this->totalEstimatedHours();
        if ($estimated <= 0) {
            return null;
        }

        return round(($this->totalActualHours() / $estimated) * 100, 1);
    }
}
