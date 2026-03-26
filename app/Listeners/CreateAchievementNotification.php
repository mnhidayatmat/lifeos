<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;

class CreateAchievementNotification
{
    public function handle(AchievementUnlocked $event): void
    {
        $event->user->notify(new \App\Notifications\AchievementNotification($event->achievement));
    }
}
