<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LifeArea extends Model
{
    protected $fillable = [
        'name', 'slug', 'color', 'icon', 'is_preset', 'is_active',
        'sort_order', 'primary_stat', 'secondary_stat',
    ];

    protected $casts = [
        'is_preset' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const PRESET_AREAS = [
        ['name' => 'Work', 'icon' => 'folder', 'color' => '#6366f1', 'primary_stat' => 'discipline', 'secondary_stat' => 'influence'],
        ['name' => 'Research', 'icon' => 'book-open', 'color' => '#3b82f6', 'primary_stat' => 'knowledge', 'secondary_stat' => 'focus'],
        ['name' => 'Health', 'icon' => 'target', 'color' => '#ef4444', 'primary_stat' => 'strength', 'secondary_stat' => 'discipline'],
        ['name' => 'Family', 'icon' => 'user', 'color' => '#ec4899', 'primary_stat' => 'wisdom', 'secondary_stat' => 'influence'],
        ['name' => 'Finance', 'icon' => 'trophy', 'color' => '#10b981', 'primary_stat' => 'wealth', 'secondary_stat' => 'discipline'],
        ['name' => 'Learning', 'icon' => 'book-open', 'color' => '#8b5cf6', 'primary_stat' => 'knowledge', 'secondary_stat' => 'discipline'],
        ['name' => 'Business', 'icon' => 'grid', 'color' => '#f59e0b', 'primary_stat' => 'wealth', 'secondary_stat' => 'influence'],
        ['name' => 'Personal', 'icon' => 'user', 'color' => '#14b8a6', 'primary_stat' => 'creativity', 'secondary_stat' => 'wisdom'],
    ];

    protected static function booted(): void
    {
        static::creating(function (LifeArea $area) {
            if (empty($area->slug)) {
                $area->slug = Str::slug($area->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
