<?php

namespace App\Listeners;

use App\Events\ReviewCompleted;
use App\Services\XpService;

class AwardReviewXp
{
    public function __construct(private XpService $xpService) {}

    public function handle(ReviewCompleted $event): void
    {
        $this->xpService->awardReviewXp($event->review);
    }
}
