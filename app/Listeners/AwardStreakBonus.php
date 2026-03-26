<?php

namespace App\Listeners;

use App\Events\StreakMilestoneReached;
use App\Services\XpService;

class AwardStreakBonus
{
    public function __construct(private XpService $xpService) {}

    public function handle(StreakMilestoneReached $event): void
    {
        $this->xpService->awardStreakBonus($event->user, $event->streak, $event->milestone);
    }
}
