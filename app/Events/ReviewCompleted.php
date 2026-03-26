<?php

namespace App\Events;

use App\Models\Review;
use Illuminate\Foundation\Events\Dispatchable;

class ReviewCompleted
{
    use Dispatchable;

    public function __construct(
        public Review $review,
    ) {}
}
