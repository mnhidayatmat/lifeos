<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Services\XpService;

class AwardTaskXp
{
    public function __construct(private XpService $xpService) {}

    public function handle(TaskCompleted $event): void
    {
        $this->xpService->awardTaskXp($event->task);
    }
}
