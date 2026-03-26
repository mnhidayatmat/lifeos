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

        $stats = $user->stats()->pluck('total_xp', 'stat');

        $projects = $user->projects()
            ->whereNotIn('status', ['completed', 'archived'])
            ->get();

        $goals = $user->goals()
            ->whereNotIn('status', ['completed', 'abandoned'])
            ->get();

        return view('dashboard', compact(
            'todayTasks', 'overdueTasks', 'activeGoals', 'stats', 'projects', 'goals'
        ));
    }
}
