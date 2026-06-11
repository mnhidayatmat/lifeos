<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    protected $fillable = [
        'life_area_id', 'title', 'type', 'author', 'url',
        'status', 'notes', 'rating', 'completed_at',
    ];

    protected $casts = [
        'rating' => 'integer',
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
}
