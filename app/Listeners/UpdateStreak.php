<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Services\StreakService;

class UpdateStreak
{
    public function __construct(private StreakService $streakService) {}

    public function handle(TaskCompleted $event): void
    {
        $this->streakService->recordActivity($event->task->user, 'daily_task');
    }
}
