<?php

namespace App\Http\Controllers;

use App\Models\Habit;
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
            $habit->logs()->where('completed_date', $today)->delete();

            return back()->with('success', 'Habit unchecked.');
        }

        $habit->logs()->create([
            'user_id' => $request->user()->id,
            'completed_date' => $today,
        ]);

        return back()->with('success', 'Habit completed.');
    }
}
