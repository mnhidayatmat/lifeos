<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // XP trend (last 14 days)
        $xpTrend = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dayXp = $user->xpLogs()->whereDate('created_at', $date)->sum('xp_amount');
            $xpTrend[] = ['date' => $date->format('M j'), 'xp' => $dayXp];
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

        // Stat growth (this week vs last week)
        $statGrowth = [];
        foreach (\App\Models\User::STATS as $stat) {
            $thisWeek = $user->xpLogs()->where('stat', $stat)->where('created_at', '>=', now()->startOfWeek())->sum('xp_amount');
            $lastWeek = $user->xpLogs()->where('stat', $stat)->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->startOfWeek()])->sum('xp_amount');
            $statGrowth[$stat] = ['this_week' => $thisWeek, 'last_week' => $lastWeek];
        }

        // Overall stats
        $totalTasks = $user->tasks()->where('status', 'completed')->count();
        $totalGoals = $user->goals()->where('status', 'completed')->count();
        $longestStreak = $user->streaks()->max('longest_count') ?? 0;
        $currentStreak = $user->streaks()->where('type', 'daily_task')->value('current_count') ?? 0;

        return view('analytics.index', compact(
            'xpTrend', 'completionRate', 'areaBalance', 'statGrowth',
            'totalTasks', 'totalGoals', 'longestStreak', 'currentStreak'
        ));
    }
}
