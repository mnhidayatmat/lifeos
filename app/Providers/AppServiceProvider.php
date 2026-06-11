<?php

namespace App\Providers;

use App\Events\AchievementUnlocked;
use App\Events\ReviewCompleted;
use App\Events\TaskCompleted;
use App\Listeners\CheckAchievements;
use App\Listeners\CreateAchievementNotification;
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
        // Task completed → update consistency streak, check milestones
        Event::listen(TaskCompleted::class, UpdateStreak::class);
        Event::listen(TaskCompleted::class, CheckAchievements::class);

        // Milestone reached → notify
        Event::listen(AchievementUnlocked::class, CreateAchievementNotification::class);

        // Review completed → check milestones
        Event::listen(ReviewCompleted::class, CheckAchievements::class);
    }
}
