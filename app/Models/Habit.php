<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'life_area_id', 'title', 'routine', 'frequency', 'frequency_days',
        'effort', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'frequency_days' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lifeArea(): BelongsTo
    {
        return $this->belongsTo(LifeArea::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function isCompletedToday(): bool
    {
        return $this->logs()->where('completed_date', today())->exists();
    }

    public function isDueToday(): bool
    {
        $day = strtolower(now()->format('D')); // mon, tue, etc.
        return match ($this->frequency) {
            'daily' => true,
            'weekdays' => !in_array($day, ['sat', 'sun']),
            'weekends' => in_array($day, ['sat', 'sun']),
            'custom' => in_array($day, $this->frequency_days ?? []),
            default => true,
        };
    }

    public function currentStreak(): int
    {
        $date = today();
        $streak = 0;

        while ($this->logs()->where('completed_date', $date)->exists()) {
            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }
}
