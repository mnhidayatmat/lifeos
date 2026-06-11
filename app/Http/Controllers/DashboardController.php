<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $todayTasks = $user->tasks()
            ->where(function ($q) {
                $q->where('due_date', today())
                    ->orWhere(function ($q2) {
                        $q2->where('is_recurring', true)->where('status', '!=', 'completed');
                    });
            })
            ->where('status', '!=', 'completed')
            ->orderBy('priority', 'desc')
            ->get();

        $overdueTasks = $user->tasks()
            ->where('due_date', '<', today())
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->orderBy('due_date')
            ->get();

        $activeGoals = $user->goals()
            ->with('lifeArea')
            ->whereIn('status', ['in_progress', 'not_started'])
            ->orderBy('priority', 'desc')
            ->limit(5)
            ->get();

        $projects = $user->projects()
            ->whereNotIn('status', ['completed', 'archived'])
            ->get();

        $goals = $user->goals()
            ->whereNotIn('status', ['completed', 'abandoned'])
            ->get();

        $dominoGoal = $user->goals()->with('lifeArea')
            ->where('is_domino', true)
            ->whereNotIn('status', ['completed', 'abandoned'])
            ->first();

        // Today's habits
        $todayHabits = $user->habits()
            ->where('is_active', true)
            ->get()
            ->filter(fn ($h) => $h->isDueToday());

        // WIP warning
        $activeProjectCount = $user->projects()->where('status', 'in_progress')->count();

        // This week summary (real productivity metrics)
        $weekStart = now()->startOfWeek();
        $tasksThisWeek = $user->tasks()->where('status', 'completed')
            ->where('completed_at', '>=', $weekStart)->count();
        $tasksToday = $user->tasks()->where('status', 'completed')
            ->whereDate('completed_at', today())->count();
        $currentStreak = $user->streaks()->where('type', 'daily_task')->value('current_count') ?? 0;
        $activeGoalCount = $user->goals()->whereIn('status', ['in_progress', 'not_started'])->count();

        // Per life area progress
        $lifeAreaProgress = $user->lifeAreas()
            ->where('is_active', true)
            ->withCount(['goals as active_goals_count' => fn ($q) => $q->whereIn('status', ['in_progress', 'not_started'])])
            ->get()
            ->map(function ($area) use ($user, $weekStart) {
                $completed = $user->tasks()
                    ->where('status', 'completed')
                    ->where('completed_at', '>=', $weekStart)
                    ->where(function ($q) use ($area) {
                        $q->whereHas('goal', fn ($g) => $g->where('life_area_id', $area->id))
                            ->orWhereHas('project', fn ($p) => $p->where('life_area_id', $area->id));
                    })
                    ->count();

                $area->tasks_this_week = $completed;

                return $area;
            });

        return view('dashboard', compact(
            'todayTasks', 'overdueTasks', 'activeGoals',
            'projects', 'goals', 'dominoGoal', 'todayHabits', 'activeProjectCount',
            'tasksThisWeek', 'tasksToday', 'currentStreak', 'activeGoalCount', 'lifeAreaProgress'
        ));
    }
}
