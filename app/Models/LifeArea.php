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
        'sort_order',
    ];

    protected $casts = [
        'is_preset' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const PRESET_AREAS = [
        ['name' => 'Work', 'icon' => 'folder', 'color' => '#6366f1'],
        ['name' => 'Research', 'icon' => 'book-open', 'color' => '#3b82f6'],
        ['name' => 'Health', 'icon' => 'target', 'color' => '#ef4444'],
        ['name' => 'Family', 'icon' => 'user', 'color' => '#ec4899'],
        ['name' => 'Finance', 'icon' => 'trophy', 'color' => '#10b981'],
        ['name' => 'Learning', 'icon' => 'book-open', 'color' => '#8b5cf6'],
        ['name' => 'Business', 'icon' => 'grid', 'color' => '#f59e0b'],
        ['name' => 'Personal', 'icon' => 'user', 'color' => '#14b8a6'],
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
