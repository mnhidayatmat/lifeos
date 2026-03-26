<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;

class ReviewService
{
    public function generateDailyData(User $user, Carbon $date): array
    {
        $tasks = $user->tasks()
            ->where('status', 'completed')
            ->whereDate('completed_at', $date)
            ->get();

        $xpLogs = $user->xpLogs()
            ->whereDate('created_at', $date)
            ->get();

        return [
            'tasks_completed' => $tasks->count(),
            'tasks_list' => $tasks->pluck('title')->toArray(),
            'xp_earned' => $xpLogs->sum('xp_amount'),
            'stat_gains' => $xpLogs->groupBy('stat')->map->sum('xp_amount')->toArray(),
        ];
    }

    public function generateWeeklyData(User $user, Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->endOfWeek();

        $completedTasks = $user->tasks()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$weekStart, $weekEnd])
            ->count();

        $overdueTasks = $user->tasks()
            ->where('due_date', '<', today())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        $xpLogs = $user->xpLogs()
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->get();

        $statGains = $xpLogs->groupBy('stat')->map->sum('xp_amount')->toArray();

        // Find strongest and weakest areas
        $areaActivity = [];
        foreach ($user->lifeAreas()->where('is_active', true)->get() as $area) {
            $count = $user->tasks()
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->whereHas('goal', fn ($q) => $q->where('life_area_id', $area->id))
                ->count();
            $areaActivity[$area->name] = $count;
        }

        arsort($areaActivity);
        $strongest = array_key_first($areaActivity);
        $neglected = array_key_last($areaActivity);

        $streak = $user->streaks()->where('type', 'daily_task')->first();

        return [
            'tasks_completed' => $completedTasks,
            'overdue_tasks' => $overdueTasks,
            'xp_earned' => $xpLogs->sum('xp_amount'),
            'stat_gains' => $statGains,
            'strongest_area' => $strongest,
            'neglected_area' => $neglected !== $strongest ? $neglected : null,
            'current_streak' => $streak?->current_count ?? 0,
        ];
    }

    public function generateMonthlyData(User $user, Carbon $monthStart): array
    {
        $monthEnd = $monthStart->copy()->endOfMonth();

        $tasksCompleted = $user->tasks()->where('status', 'completed')
            ->whereBetween('completed_at', [$monthStart, $monthEnd])->count();

        $goalsCompleted = $user->goals()->where('status', 'completed')
            ->whereBetween('completed_at', [$monthStart, $monthEnd])->count();

        $goalsInProgress = $user->goals()->where('status', 'in_progress')->count();

        $xpLogs = $user->xpLogs()->whereBetween('created_at', [$monthStart, $monthEnd])->get();
        $totalXp = $xpLogs->sum('xp_amount');
        $statGains = $xpLogs->groupBy('stat')->map->sum('xp_amount')->toArray();

        $reviewsCompleted = $user->reviews()->whereNotNull('completed_at')
            ->whereBetween('period_date', [$monthStart, $monthEnd])->count();

        // Best week (most XP)
        $bestWeekXp = 0;
        $bestWeekLabel = null;
        $current = $monthStart->copy();
        $weekNum = 1;
        while ($current->lte($monthEnd)) {
            $weekEnd = $current->copy()->addDays(6)->min($monthEnd);
            $weekXp = $user->xpLogs()->whereBetween('created_at', [$current, $weekEnd])->sum('xp_amount');
            if ($weekXp > $bestWeekXp) {
                $bestWeekXp = $weekXp;
                $bestWeekLabel = "Week $weekNum";
            }
            $current->addWeek();
            $weekNum++;
        }

        // Level gained this month
        $startLevelXp = $user->xpLogs()->where('created_at', '<', $monthStart)->sum('xp_amount');
        $startLevel = \App\Services\XpService::calculateLevel($startLevelXp);

        return [
            'tasks_completed' => $tasksCompleted,
            'goals_completed' => $goalsCompleted,
            'goals_in_progress' => $goalsInProgress,
            'total_xp' => $totalXp,
            'stat_gains' => $statGains,
            'reviews_completed' => $reviewsCompleted,
            'best_week' => $bestWeekLabel,
            'best_week_xp' => $bestWeekXp,
            'level_start' => $startLevel,
            'level_now' => $user->level,
        ];
    }
}
