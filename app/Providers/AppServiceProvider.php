<?php

namespace App\Providers;

use App\Events\AchievementUnlocked;
use App\Events\LevelUp;
use App\Events\ReviewCompleted;
use App\Events\StreakMilestoneReached;
use App\Events\TaskCompleted;
use App\Listeners\AwardReviewXp;
use App\Listeners\AwardStreakBonus;
use App\Listeners\AwardTaskXp;
use App\Listeners\CheckAchievements;
use App\Listeners\CreateAchievementNotification;
use App\Listeners\CreateLevelUpNotification;
use App\Listeners\UpdateStreak;
use App\Notifications\Channels\BrevoChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register Brevo notification channel
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('brevo', fn ($app) => new BrevoChannel);
        });
        // Task completed → award XP, update streak, check achievements
        Event::listen(TaskCompleted::class, AwardTaskXp::class);
        Event::listen(TaskCompleted::class, UpdateStreak::class);
        Event::listen(TaskCompleted::class, CheckAchievements::class);

        // Level up → update rank, notify
        Event::listen(LevelUp::class, CreateLevelUpNotification::class);

        // Achievement unlocked → notify
        Event::listen(AchievementUnlocked::class, CreateAchievementNotification::class);

        // Review completed → award XP, check achievements
        Event::listen(ReviewCompleted::class, AwardReviewXp::class);
        Event::listen(ReviewCompleted::class, CheckAchievements::class);

        // Streak milestone → award bonus, check achievements
        Event::listen(StreakMilestoneReached::class, AwardStreakBonus::class);
        Event::listen(StreakMilestoneReached::class, CheckAchievements::class);
    }
}
