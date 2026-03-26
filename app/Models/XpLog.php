<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XpLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['source_type', 'source_id', 'xp_amount', 'stat', 'description', 'created_at'];

    protected $casts = [
        'xp_amount' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
