<?php

namespace App\Events;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class AchievementUnlocked
{
    use Dispatchable;

    public function __construct(
        public User $user,
        public Achievement $achievement,
    ) {}
}
