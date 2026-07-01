<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSession extends Model
{
    public const STATUSES = ['active', 'paused', 'completed'];

    protected $fillable = [
        'user_id',
        'sprint_id',
        'task_id',
        'category_id',
        'description',
        'started_at',
        'ended_at',
        'paused_at',
        'elapsed_seconds',
        'interruption_seconds',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'paused_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function interruptions(): HasMany
    {
        return $this->hasMany(Interruption::class);
    }

    public function workedSeconds(): int
    {
        return max(0, $this->elapsed_seconds - $this->interruption_seconds);
    }

    public function workedHours(): float
    {
        return round($this->workedSeconds() / 3600, 2);
    }
}
