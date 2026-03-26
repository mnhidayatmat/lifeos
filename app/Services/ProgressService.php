<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\LifeArea;

class ProgressService
{
    public function computeGoalProgress(Goal $goal): int
    {
        return $goal->progress;
    }

    public function getLifeAreaHealth(LifeArea $area): array
    {
        $activeGoals = $area->goals()->whereNotIn('status', ['completed', 'abandoned'])->count();
        $recentTasks = $area->goals()
            ->join('tasks', 'goals.id', '=', 'tasks.goal_id')
            ->where('tasks.status', 'completed')
            ->where('tasks.completed_at', '>=', now()->subDays(7))
            ->count();

        return [
            'active_goals' => $activeGoals,
            'recent_completions' => $recentTasks,
            'health' => $recentTasks > 0 ? 'active' : ($activeGoals > 0 ? 'stale' : 'empty'),
        ];
    }
}
