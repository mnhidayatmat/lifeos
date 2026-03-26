<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\Task;
use App\Services\XpService;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $habits = $user->habits()->with('lifeArea')->where('is_active', true)->get();

        $grouped = [
            'morning' => $habits->where('routine', 'morning'),
            'afternoon' => $habits->where('routine', 'afternoon'),
            'evening' => $habits->where('routine', 'evening'),
        ];

        $areas = $user->lifeAreas()->where('is_active', true)->get();

        return view('habits.index', compact('grouped', 'habits', 'areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'routine' => 'required|in:morning,afternoon,evening',
            'frequency' => 'required|in:daily,weekdays,weekends,custom',
            'frequency_days' => 'nullable|array',
            'frequency_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'effort' => 'required|in:small,medium,large',
            'life_area_id' => 'nullable|exists:life_areas,id',
        ]);

        $request->user()->habits()->create($validated);

        return back()->with('success', 'Habit created.');
    }

    public function update(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'routine' => 'required|in:morning,afternoon,evening',
            'frequency' => 'required|in:daily,weekdays,weekends,custom',
            'frequency_days' => 'nullable|array',
            'effort' => 'required|in:small,medium,large',
            'life_area_id' => 'nullable|exists:life_areas,id',
        ]);

        $habit->update($validated);

        return back()->with('success', 'Habit updated.');
    }

    public function destroy(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === auth()->id(), 403);
        $habit->delete();

        return back()->with('success', 'Habit deleted.');
    }

    public function toggle(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === auth()->id(), 403);

        $today = today();

        if ($habit->isCompletedToday()) {
            // Undo — remove log and revoke XP
            $log = $habit->logs()->where('completed_date', $today)->first();
            if ($log && $log->xp_awarded > 0) {
                $user = $request->user();
                $lifeArea = $habit->lifeArea;
                // Simple XP revoke for habit
                $user->decrement('total_xp', $log->xp_awarded);
                if ($lifeArea) {
                    $user->stats()->where('stat', $lifeArea->primary_stat)
                        ->where('total_xp', '>=', (int) floor($log->xp_awarded * 0.7))
                        ->decrement('total_xp', (int) floor($log->xp_awarded * 0.7));
                    $user->stats()->where('stat', $lifeArea->secondary_stat)
                        ->where('total_xp', '>=', $log->xp_awarded - (int) floor($log->xp_awarded * 0.7))
                        ->decrement('total_xp', $log->xp_awarded - (int) floor($log->xp_awarded * 0.7));
                }
                $user->xpLogs()->where('source_type', 'habit')->where('source_id', $log->id)->delete();

                // Recalculate level
                $user->refresh();
                $newLevel = XpService::calculateLevel($user->total_xp);
                $user->update(['level' => $newLevel, 'rank' => app(\App\Services\RankService::class)->resolveRank($newLevel)]);
            }
            $log?->delete();

            return back()->with('success', 'Habit unchecked.');
        }

        // Complete habit
        $xp = Task::EFFORT_XP[$habit->effort] ?? 5;
        $log = $habit->logs()->create([
            'user_id' => $request->user()->id,
            'completed_date' => $today,
            'xp_awarded' => $xp,
        ]);

        // Award XP
        $user = $request->user();
        $lifeArea = $habit->lifeArea;
        if ($lifeArea) {
            $primaryXp = (int) floor($xp * 0.7);
            $secondaryXp = $xp - $primaryXp;
            $user->stats()->where('stat', $lifeArea->primary_stat)->increment('total_xp', $primaryXp);
            $user->stats()->where('stat', $lifeArea->secondary_stat)->increment('total_xp', $secondaryXp);
            $user->xpLogs()->create(['source_type' => 'habit', 'source_id' => $log->id, 'xp_amount' => $primaryXp, 'stat' => $lifeArea->primary_stat, 'description' => $habit->title, 'created_at' => now()]);
            $user->xpLogs()->create(['source_type' => 'habit', 'source_id' => $log->id, 'xp_amount' => $secondaryXp, 'stat' => $lifeArea->secondary_stat, 'description' => $habit->title, 'created_at' => now()]);
        } else {
            $user->stats()->where('stat', 'discipline')->increment('total_xp', $xp);
            $user->xpLogs()->create(['source_type' => 'habit', 'source_id' => $log->id, 'xp_amount' => $xp, 'stat' => 'discipline', 'description' => $habit->title, 'created_at' => now()]);
        }

        $user->increment('total_xp', $xp);
        $user->refresh();
        $newLevel = XpService::calculateLevel($user->total_xp);
        if ($newLevel !== $user->level) {
            $user->update(['level' => $newLevel, 'rank' => app(\App\Services\RankService::class)->resolveRank($newLevel)]);
        }

        return back()->with('success', "Habit completed! +{$xp} XP");
    }
}
