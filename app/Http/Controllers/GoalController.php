<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $areaFilter = $request->query('area');

        $query = $user->goals()->with('lifeArea');

        if ($areaFilter) {
            $query->where('life_area_id', $areaFilter);
        }

        $goals = $query->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'not_started' THEN 1 WHEN 'on_hold' THEN 2 WHEN 'completed' THEN 3 WHEN 'abandoned' THEN 4 END")
            ->latest()
            ->get();

        $areas = $user->lifeAreas()->where('is_active', true)->get();

        return view('goals.index', compact('goals', 'areas', 'areaFilter'));
    }

    public function create(Request $request)
    {
        $areas = $request->user()->lifeAreas()->where('is_active', true)->get();
        return view('goals.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'life_area_id' => 'required|exists:life_areas,id',
            'progress_type' => 'required|in:task_based,kpi_based,manual',
            'target_value' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $area = $request->user()->lifeAreas()->findOrFail($validated['life_area_id']);

        $request->user()->goals()->create($validated);

        return redirect()->route('goals.index')->with('success', 'Goal created.');
    }

    public function show(Request $request, Goal $goal)
    {
        $this->authorize($goal);

        $goal->load(['lifeArea', 'projects.tasks', 'tasks' => function ($q) {
            $q->whereNull('project_id')->latest();
        }]);

        return view('goals.show', compact('goal'));
    }

    public function edit(Request $request, Goal $goal)
    {
        $this->authorize($goal);
        $areas = $request->user()->lifeAreas()->where('is_active', true)->get();

        return view('goals.edit', compact('goal', 'areas'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize($goal);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'life_area_id' => 'required|exists:life_areas,id',
            'progress_type' => 'required|in:task_based,kpi_based,manual',
            'target_value' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)->with('success', 'Goal updated.');
    }

    public function destroy(Request $request, Goal $goal)
    {
        $this->authorize($goal);
        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Goal deleted.');
    }

    public function updateStatus(Request $request, Goal $goal)
    {
        $this->authorize($goal);

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,on_hold,completed,abandoned',
        ]);

        $goal->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        $message = match ($validated['status']) {
            'completed' => 'Goal completed! Great work.',
            'abandoned' => 'Goal archived. Priorities change — that\'s growth too.',
            default => 'Goal status updated.',
        };

        return back()->with('success', $message);
    }

    public function updateProgress(Request $request, Goal $goal)
    {
        $this->authorize($goal);

        if ($goal->progress_type === 'kpi_based') {
            $validated = $request->validate(['current_value' => 'required|numeric|min:0']);
            $goal->update(['current_value' => $validated['current_value']]);
        } elseif ($goal->progress_type === 'manual') {
            $validated = $request->validate(['manual_progress' => 'required|integer|min:0|max:100']);
            $goal->update(['manual_progress' => $validated['manual_progress']]);
        }

        return back()->with('success', 'Progress updated.');
    }

    public function toggleDomino(Request $request, Goal $goal)
    {
        $this->authorize($goal);

        if (!$goal->is_domino) {
            // Clear any existing domino goal
            $request->user()->goals()->where('is_domino', true)->update(['is_domino' => false]);
        }

        $goal->update(['is_domino' => !$goal->is_domino]);

        return back()->with('success', $goal->is_domino ? 'Set as your #1 Domino Goal.' : 'Domino goal removed.');
    }

    private function authorize(Goal $goal): void
    {
        abort_unless($goal->user_id === auth()->id(), 403);
    }
}
