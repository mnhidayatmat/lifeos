<?php

namespace App\Services;

use App\Events\LevelUp;
use App\Models\Task;
use App\Models\Review;
use App\Models\Streak;
use App\Models\User;
use App\Models\XpLog;

class XpService
{
    public function awardTaskXp(Task $task): array
    {
        $user = $task->user;
        $baseXp = Task::EFFORT_XP[$task->effort] ?? 15;

        // Goal-linked bonus (1.2x)
        $multiplier = $task->goal_id ? 1.2 : 1.0;
        $totalXp = (int) floor($baseXp * $multiplier);

        // Resolve stat mapping from life area
        $lifeArea = $task->resolveLifeArea();

        if ($lifeArea) {
            $primaryXp = (int) floor($totalXp * 0.70);
            $secondaryXp = $totalXp - $primaryXp;

            $this->addStatXp($user, $lifeArea->primary_stat, $primaryXp, 'task', $task->id, $task->title);
            $this->addStatXp($user, $lifeArea->secondary_stat, $secondaryXp, 'task', $task->id, $task->title);
        } else {
            // Standalone task with no area — award to Discipline
            $this->addStatXp($user, 'discipline', $totalXp, 'task', $task->id, $task->title);
        }

        // Update user total XP and check level
        $oldLevel = $user->level;
        $user->increment('total_xp', $totalXp);
        $user->refresh();

        $newLevel = self::calculateLevel($user->total_xp);
        if ($newLevel !== $user->level) {
            $user->update(['level' => $newLevel]);
        }

        // Update task record
        $task->update(['xp_awarded' => $totalXp]);

        // Check for level up
        if ($newLevel > $oldLevel) {
            $newRank = app(RankService::class)->resolveRank($newLevel);
            $oldRank = $user->rank;
            $user->update(['rank' => $newRank]);

            event(new LevelUp($user, $newLevel, $oldLevel, $newRank, $oldRank));
        }

        return [
            'xp' => $totalXp,
            'primary_stat' => $lifeArea?->primary_stat ?? 'discipline',
            'secondary_stat' => $lifeArea?->secondary_stat,
            'new_level' => $newLevel,
            'leveled_up' => $newLevel > $oldLevel,
        ];
    }

    public function revokeTaskXp(Task $task): void
    {
        $task->refresh();
        $user = $task->user;
        $user->refresh();
        $xpToRevoke = $task->xp_awarded;

        if ($xpToRevoke <= 0) {
            return;
        }

        // Remove XP logs for this task
        $logs = $user->xpLogs()
            ->where('source_type', 'task')
            ->where('source_id', $task->id)
            ->get();

        foreach ($logs as $log) {
            // Decrement stat XP (don't go below 0)
            $user->stats()->where('stat', $log->stat)
                ->where('total_xp', '>=', $log->xp_amount)
                ->decrement('total_xp', $log->xp_amount);

            $log->delete();
        }

        // Decrement user total XP (don't go below 0) and recalculate level/rank
        $newTotalXp = max(0, $user->total_xp - $xpToRevoke);
        $newLevel = self::calculateLevel($newTotalXp);
        $newRank = app(RankService::class)->resolveRank($newLevel);

        $user->update([
            'total_xp' => $newTotalXp,
            'level' => $newLevel,
            'rank' => $newRank,
        ]);

        // Reset task XP record
        $task->update(['xp_awarded' => 0]);
    }

    public function awardReviewXp(Review $review): int
    {
        $user = $review->user;
        $xp = $review->type === 'weekly' ? 25 : 10;

        // Split between Wisdom and Discipline
        $wisdomXp = (int) floor($xp * 0.5);
        $disciplineXp = $xp - $wisdomXp;

        $this->addStatXp($user, 'wisdom', $wisdomXp, 'review', $review->id, "{$review->type} review");
        $this->addStatXp($user, 'discipline', $disciplineXp, 'review', $review->id, "{$review->type} review");

        $oldLevel = $user->level;
        $user->increment('total_xp', $xp);
        $user->refresh();

        $newLevel = self::calculateLevel($user->total_xp);
        if ($newLevel !== $user->level) {
            $user->update(['level' => $newLevel]);
            $newRank = app(RankService::class)->resolveRank($newLevel);
            $user->update(['rank' => $newRank]);

            if ($newLevel > $oldLevel) {
                event(new LevelUp($user, $newLevel, $oldLevel, $newRank, $user->rank));
            }
        }

        $review->update(['xp_awarded' => $xp]);

        return $xp;
    }

    public function awardStreakBonus(User $user, Streak $streak, int $milestone): int
    {
        $xp = match ($milestone) {
            7 => 20,
            30 => 100,
            100 => 500,
            default => 0,
        };

        if ($xp <= 0) {
            return 0;
        }

        $this->addStatXp($user, 'discipline', $xp, 'streak', $streak->id, "{$milestone}-day streak bonus");

        $user->increment('total_xp', $xp);
        $user->refresh();

        $newLevel = self::calculateLevel($user->total_xp);
        if ($newLevel !== $user->level) {
            $user->update(['level' => $newLevel]);
            $newRank = app(RankService::class)->resolveRank($newLevel);
            $user->update(['rank' => $newRank]);
        }

        return $xp;
    }

    public static function calculateLevel(int $totalXp): int
    {
        if ($totalXp <= 0) {
            return 1;
        }
        return (int) floor(sqrt($totalXp / 25)) + 1;
    }

    public static function xpForLevel(int $level): int
    {
        return ($level - 1) ** 2 * 25;
    }

    private function addStatXp(User $user, string $stat, int $xp, string $sourceType, ?int $sourceId, string $description): void
    {
        if ($xp <= 0) {
            return;
        }

        $user->stats()->where('stat', $stat)->increment('total_xp', $xp);

        $user->xpLogs()->create([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'xp_amount' => $xp,
            'stat' => $stat,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
