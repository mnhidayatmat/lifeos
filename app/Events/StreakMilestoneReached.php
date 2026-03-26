<?php

namespace App\Events;

use App\Models\Streak;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class StreakMilestoneReached
{
    use Dispatchable;

    public function __construct(
        public User $user,
        public Streak $streak,
        public int $milestone,
    ) {}
}
