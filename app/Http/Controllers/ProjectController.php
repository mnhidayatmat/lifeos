<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $areaFilter = $request->query('area');
        $goalFilter = $request->query('goal');

        $query = $user->projects()->with(['lifeArea', 'goal']);

        if ($areaFilter) {
            $query->where('life_area_id', $areaFilter);
        }
        if ($goalFilter) {
            $query->where('goal_id', $goalFilter);
        }

        $projects = $query->latest()->get();
        $areas = $user->lifeAreas()->where('is_active', true)->get();
        $goals = $user->goals()->whereNotIn('status', ['completed', 'abandoned'])->get();

        return view('projects.index', compact('projects', 'areas', 'goals', 'areaFilter', 'goalFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'life_area_id' => 'required|exists:life_areas,id',
            'goal_id' => 'nullable|exists:goals,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'impact_score' => 'nullable|integer|min:1|max:10',
            'confidence_score' => 'nullable|integer|min:1|max:10',
            'ease_score' => 'nullable|integer|min:1|max:10',
            'due_date' => 'nullable|date',
        ]);

        $request->user()->lifeAreas()->findOrFail($validated['life_area_id']);

        // WIP limit warning
        $activeCount = $request->user()->projects()->where('status', 'in_progress')->count();
        $wipWarning = $activeCount >= 3 ? 'Project created. You have ' . ($activeCount + 1) . ' active projects — consider finishing some before starting new ones.' : null;

        $request->user()->projects()->create($validated);

        return back()->with('success', $wipWarning ?? 'Project created.');
    }

    public function show(Request $request, Project $project)
    {
        $this->authorize($project);
        $project->load(['lifeArea', 'goal', 'tasks.subtasks']);

        return view('projects.show', compact('project'));
    }

    public function edit(Request $request, Project $project)
    {
        $this->authorize($project);
        $areas = $request->user()->lifeAreas()->where('is_active', true)->get();
        $goals = $request->user()->goals()->whereNotIn('status', ['completed', 'abandoned'])->get();

        return view('projects.edit', compact('project', 'areas', 'goals'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize($project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'life_area_id' => 'required|exists:life_areas,id',
            'goal_id' => 'nullable|exists:goals,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'impact_score' => 'nullable|integer|min:1|max:10',
            'confidence_score' => 'nullable|integer|min:1|max:10',
            'ease_score' => 'nullable|integer|min:1|max:10',
            'due_date' => 'nullable|date',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    public function destroy(Request $request, Project $project)
    {
        $this->authorize($project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $this->authorize($project);

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,on_hold,completed,archived',
        ]);

        $project->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return back()->with('success', 'Project status updated.');
    }

    private function authorize(Project $project): void
    {
        abort_unless($project->user_id === auth()->id(), 403);
    }
}
