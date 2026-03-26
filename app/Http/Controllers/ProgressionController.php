<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Services\XpService;
use Illuminate\Http\Request;

class ProgressionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $stats = $user->stats()->pluck('total_xp', 'stat');
        $recentXpLogs = $user->xpLogs()->latest('created_at')->limit(15)->get();

        $xpProgress = $user->xpProgress();
        $xpNeeded = $user->xpNeeded();

        return view('progression.index', compact('user', 'stats', 'recentXpLogs', 'xpProgress', 'xpNeeded'));
    }

    public function achievements(Request $request)
    {
        $user = $request->user();
        $allAchievements = Achievement::all();
        $unlockedIds = $user->achievements()->pluck('achievement_id')->toArray();
        $userAchievements = $user->achievements()->with('achievement')->get()->keyBy('achievement_id');

        return view('progression.achievements', compact('allAchievements', 'unlockedIds', 'userAchievements'));
    }
}
