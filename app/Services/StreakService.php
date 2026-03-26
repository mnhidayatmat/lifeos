<?php

namespace App\Services;

use App\Events\StreakMilestoneReached;
use App\Models\Streak;
use App\Models\User;
use Carbon\Carbon;

class StreakService
{
    public function recordActivity(User $user, string $type): Streak
    {
        $streak = $user->streaks()->firstOrCreate(
            ['type' => $type],
            ['current_count' => 0, 'longest_count' => 0]
        );

        $today = today();
        $lastActive = $streak->last_active_date;

        // Already recorded today
        if ($lastActive && $lastActive->equalTo($today)) {
            return $streak;
        }

        // Consecutive day
        if ($lastActive && $lastActive->equalTo($today->subDay())) {
            $streak->increment('current_count');
            $streak->update([
                'last_active_date' => today(),
                'grace_used' => false,
            ]);
        }
        // Grace period (missed one day)
        elseif ($lastActive && $lastActive->equalTo($today->subDays(2)) && !$streak->grace_used) {
            $streak->increment('current_count');
            $streak->update([
                'last_active_date' => today(),
                'grace_used' => true,
            ]);
        }
        // Streak broken or first activity
        else {
            $streak->update([
                'current_count' => 1,
                'last_active_date' => today(),
                'grace_used' => false,
            ]);
        }

        // Update longest
        if ($streak->current_count > $streak->longest_count) {
            $streak->update(['longest_count' => $streak->current_count]);
        }

        // Check milestones
        $this->checkMilestones($user, $streak);

        return $streak;
    }

    public function checkMilestones(User $user, Streak $streak): void
    {
        $milestones = [7, 30, 100];

        foreach ($milestones as $milestone) {
            if ($streak->current_count === $milestone) {
                event(new StreakMilestoneReached($user, $streak, $milestone));
            }
        }
    }

    public function expireStale(): int
    {
        $expired = 0;
        $cutoff = today()->subDays(2); // Beyond grace period

        $streaks = Streak::where('current_count', '>', 0)
            ->where('last_active_date', '<', $cutoff)
            ->get();

        foreach ($streaks as $streak) {
            $streak->update([
                'current_count' => 0,
                'grace_used' => false,
            ]);
            $expired++;
        }

        return $expired;
    }
}
