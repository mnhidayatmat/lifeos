<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Tasks completed trend (last 14 days)
        $completionTrend = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = $user->tasks()->where('status', 'completed')
                ->whereDate('completed_at', $date)->count();
            $completionTrend[] = ['date' => $date->format('M j'), 'count' => $count];
        }

        // Task completion rate (last 7 days)
        $completionRate = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $total = $user->tasks()->whereDate('due_date', $date)->count();
            $completed = $user->tasks()->where('status', 'completed')->whereDate('completed_at', $date)->count();
            $completionRate[] = [
                'date' => $date->format('D'),
                'total' => $total,
                'completed' => $completed,
                'rate' => $total > 0 ? round(($completed / $total) * 100) : 0,
            ];
        }

        // Life area balance (task completions per area this month)
        $areaBalance = [];
        $areas = $user->lifeAreas()->where('is_active', true)->get();
        foreach ($areas as $area) {
            $completions = $user->tasks()
                ->where('status', 'completed')
                ->where('completed_at', '>=', now()->startOfMonth())
                ->where(function ($q) use ($area) {
                    $q->whereHas('goal', fn ($g) => $g->where('life_area_id', $area->id))
                        ->orWhereHas('project', fn ($p) => $p->where('life_area_id', $area->id));
                })
                ->count();
            $areaBalance[] = ['name' => $area->name, 'color' => $area->color, 'count' => $completions];
        }
        $areaBalanceMax = collect($areaBalance)->max('count') ?: 1;

        // Headline metrics
        $tasksThisWeek = $user->tasks()->where('status', 'completed')
            ->where('completed_at', '>=', now()->startOfWeek())->count();
        $tasksThisMonth = $user->tasks()->where('status', 'completed')
            ->where('completed_at', '>=', now()->startOfMonth())->count();
        $totalTasks = $user->tasks()->where('status', 'completed')->count();
        $totalGoals = $user->goals()->where('status', 'completed')->count();
        $totalProjects = $user->projects()->where('status', 'completed')->count();

        // Consistency
        $longestStreak = $user->streaks()->max('longest_count') ?? 0;
        $currentStreak = $user->streaks()->where('type', 'daily_task')->value('current_count') ?? 0;

        // Milestones
        $milestonesUnlocked = $user->achievements()->count();
        $milestonesTotal = Achievement::count();

        return view('analytics.index', compact(
            'completionTrend', 'completionRate', 'areaBalance', 'areaBalanceMax',
            'tasksThisWeek', 'tasksThisMonth', 'totalTasks', 'totalGoals', 'totalProjects',
            'longestStreak', 'currentStreak', 'milestonesUnlocked', 'milestonesTotal'
        ));
    }
}
