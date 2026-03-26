<?php

namespace App\Listeners;

use App\Services\AchievementService;

class CheckAchievements
{
    public function __construct(private AchievementService $achievementService) {}

    public function handle(object $event): void
    {
        $user = $event->user ?? $event->task?->user ?? $event->review?->user ?? null;

        if ($user) {
            $this->achievementService->checkAll($user);
        }
    }
}
