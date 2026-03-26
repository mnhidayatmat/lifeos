<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = [
        'life_area_id', 'title', 'description', 'progress_type',
        'target_value', 'current_value', 'manual_progress',
        'status', 'priority', 'is_domino', 'due_date', 'completed_at',
    ];


    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'manual_progress' => 'integer',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lifeArea(): BelongsTo
    {
        return $this->belongsTo(LifeArea::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getProgressAttribute(): int
    {
        return match ($this->progress_type) {
            'kpi_based' => $this->target_value > 0
                ? min(100, (int) round(($this->current_value / $this->target_value) * 100))
                : 0,
            'manual' => min(100, $this->manual_progress),
            default => $this->computeTaskProgress(),
        };
    }

    private function computeTaskProgress(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->tasks()->where('status', 'completed')->count();
        return (int) round(($completed / $total) * 100);
    }
}
