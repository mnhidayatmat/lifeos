<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Streak extends Model
{
    protected $fillable = ['type', 'current_count', 'longest_count', 'last_active_date', 'grace_used'];

    protected $casts = [
        'current_count' => 'integer',
        'longest_count' => 'integer',
        'last_active_date' => 'date',
        'grace_used' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
