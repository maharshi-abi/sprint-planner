<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySummary extends Model
{
    protected $fillable = [
        'user_id',
        'summary_date',
        'target_hours',
        'completed_hours',
        'remaining_hours',
    ];

    protected function casts(): array
    {
        return [
            'summary_date' => 'date',
            'target_hours' => 'decimal:2',
            'completed_hours' => 'decimal:2',
            'remaining_hours' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
