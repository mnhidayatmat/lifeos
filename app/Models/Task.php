<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'project_id', 'goal_id', 'title', 'description',
        'effort', 'priority', 'is_important', 'status', 'due_date', 'completed_at',
        'is_recurring', 'recurrence_rule', 'parent_task_id', 'xp_awarded',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'is_important' => 'boolean',
        'xp_awarded' => 'integer',
    ];

    public const EFFORT_XP = [
        'small' => 5,
        'medium' => 15,
        'large' => 30,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function recurringInstances(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('sort_order');
    }

    public function getBaseXpAttribute(): int
    {
        return self::EFFORT_XP[$this->effort] ?? 15;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isCompleted();
    }

    public function isUrgent(): bool
    {
        return in_array($this->priority, ['urgent', 'high']);
    }

    public function eisenhowerQuadrant(): string
    {
        return match (true) {
            $this->isUrgent() && $this->is_important => 'do_first',
            !$this->isUrgent() && $this->is_important => 'schedule',
            $this->isUrgent() && !$this->is_important => 'delegate',
            default => 'eliminate',
        };
    }

    public function resolveLifeArea(): ?LifeArea
    {
        if ($this->project?->lifeArea) {
            return $this->project->lifeArea;
        }
        if ($this->goal?->lifeArea) {
            return $this->goal->lifeArea;
        }
        return null;
    }
}
