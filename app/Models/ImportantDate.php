<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ImportantDate extends Model
{
    protected $fillable = [
        'life_area_id', 'title', 'description', 'date', 'time',
        'all_day', 'reminders', 'recurrence', 'completed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'all_day' => 'boolean',
        'reminders' => 'array',
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

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * The next occurrence of this date — handles yearly/monthly recurrence by
     * rolling the stored date forward to today or later.
     */
    public function nextOccurrence(): Carbon
    {
        $date = $this->date->copy();

        if (! $this->recurrence) {
            return $date;
        }

        $today = today();

        while ($date->lt($today)) {
            $date = match ($this->recurrence) {
                'yearly' => $date->addYear(),
                'monthly' => $date->addMonthNoOverflow(),
                default => $date->addCentury(), // safety: break the loop
            };
        }

        return $date;
    }

    public function daysUntil(): int
    {
        return (int) today()->diffInDays($this->nextOccurrence(), false);
    }

    public function isOverdue(): bool
    {
        return ! $this->isCompleted() && $this->daysUntil() < 0;
    }

    public function isToday(): bool
    {
        return $this->daysUntil() === 0;
    }

    /**
     * Human countdown label, e.g. "Today", "Tomorrow", "in 5 days", "3 days ago".
     */
    public function countdownLabel(): string
    {
        $days = $this->daysUntil();

        return match (true) {
            $days === 0 => 'Today',
            $days === 1 => 'Tomorrow',
            $days === -1 => 'Yesterday',
            $days > 1 => "in {$days} days",
            default => abs($days).' days ago',
        };
    }

    /**
     * The reminder offsets (days before) as a clean integer collection.
     */
    public function reminderOffsets(): Collection
    {
        return collect($this->reminders ?? [])
            ->map(fn ($d) => (int) $d)
            ->unique()
            ->sortDesc()
            ->values();
    }

    /**
     * Bucket for grouping on the index page.
     */
    public function bucket(): string
    {
        if ($this->isCompleted()) {
            return 'completed';
        }

        $days = $this->daysUntil();

        return match (true) {
            $days < 0 => 'overdue',
            $days === 0 => 'today',
            $days <= 7 => 'this_week',
            $days <= 31 => 'this_month',
            default => 'later',
        };
    }
}
