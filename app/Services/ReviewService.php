<?php

namespace App\Services;

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

        return [
            'tasks_completed' => $tasks->count(),
            'tasks_list' => $tasks->pluck('title')->toArray(),
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

        $reviewsCompleted = $user->reviews()->whereNotNull('completed_at')
            ->whereBetween('period_date', [$monthStart, $monthEnd])->count();

        // Best week (most tasks completed)
        $bestWeekCount = 0;
        $bestWeekLabel = null;
        $current = $monthStart->copy();
        $weekNum = 1;
        while ($current->lte($monthEnd)) {
            $weekEnd = $current->copy()->addDays(6)->min($monthEnd);
            $weekCount = $user->tasks()->where('status', 'completed')
                ->whereBetween('completed_at', [$current, $weekEnd])->count();
            if ($weekCount > $bestWeekCount) {
                $bestWeekCount = $weekCount;
                $bestWeekLabel = "Week $weekNum";
            }
            $current->addWeek();
            $weekNum++;
        }

        return [
            'tasks_completed' => $tasksCompleted,
            'goals_completed' => $goalsCompleted,
            'goals_in_progress' => $goalsInProgress,
            'reviews_completed' => $reviewsCompleted,
            'best_week' => $bestWeekLabel,
            'best_week_count' => $bestWeekCount,
        ];
    }
}
