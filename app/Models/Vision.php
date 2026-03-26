<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vision extends Model
{
    protected $fillable = ['vision_statement', 'i_am_statements', 'anti_vision'];

    protected $casts = [
        'i_am_statements' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
