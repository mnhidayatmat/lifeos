<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;

class MilestonesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $milestones = Achievement::orderBy('id')->get();
        $unlocked = $user->achievements()->with('achievement')->get()->keyBy('achievement_id');

        $unlockedCount = $unlocked->count();
        $totalCount = $milestones->count();

        return view('milestones.index', compact('milestones', 'unlocked', 'unlockedCount', 'totalCount'));
    }
}
