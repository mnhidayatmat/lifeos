<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;

class AchievementService
{
    public function checkAll(User $user): void
    {
        $unlocked = $user->achievements()->pluck('achievement_id')->toArray();
        $achievements = Achievement::all();

        foreach ($achievements as $achievement) {
            if (in_array($achievement->id, $unlocked)) {
                continue;
            }

            if ($this->isQualified($user, $achievement)) {
                $this->award($user, $achievement);
            }
        }
    }

    private function isQualified(User $user, Achievement $achievement): bool
    {
        return match ($achievement->key) {
            'first_task' => $user->tasks()->where('status', 'completed')->exists(),
            'first_goal' => $user->goals()->where('status', 'completed')->exists(),
            'streak_7' => $user->streaks()->where('current_count', '>=', 7)->exists(),
            'streak_30' => $user->streaks()->where('current_count', '>=', 30)->exists(),
            'first_weekly_review' => $user->reviews()->where('type', 'weekly')->whereNotNull('completed_at')->exists(),
            'reached_apprentice' => $user->level >= 6,
            'all_areas_active' => $user->lifeAreas()->where('is_active', true)->count() >= 3
                && $user->lifeAreas()->where('is_active', true)->get()->every(fn ($area) => $area->goals()->exists()),
            'tasks_100' => $user->tasks()->where('status', 'completed')->count() >= 100,
            default => false,
        };
    }

    private function award(User $user, Achievement $achievement): void
    {
        $user->achievements()->create([
            'achievement_id' => $achievement->id,
            'unlocked_at' => now(),
        ]);

        // Award bonus XP
        if ($achievement->xp_reward > 0) {
            $user->increment('total_xp', $achievement->xp_reward);
        }

        event(new AchievementUnlocked($user, $achievement));
    }
}
