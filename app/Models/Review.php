<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'type', 'period_date', 'responses', 'auto_summary',
        'notes', 'completed_at',
    ];

    protected $casts = [
        'period_date' => 'date',
        'responses' => 'array',
        'auto_summary' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
