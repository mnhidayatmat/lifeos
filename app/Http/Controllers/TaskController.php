<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted as TaskCompletedEvent;
use App\Models\Task;
use App\Services\XpService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $view = $request->query('view', 'today');

        $query = $user->tasks()->with(['project', 'goal']);

        // Eisenhower Matrix view
        if ($view === 'matrix') {
            $allTasks = $user->tasks()->with(['project', 'goal'])
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->get();

            $matrix = [
                'do_first' => $allTasks->filter(fn ($t) => $t->eisenhowerQuadrant() === 'do_first')->values(),
                'schedule' => $allTasks->filter(fn ($t) => $t->eisenhowerQuadrant() === 'schedule')->values(),
                'delegate' => $allTasks->filter(fn ($t) => $t->eisenhowerQuadrant() === 'delegate')->values(),
                'eliminate' => $allTasks->filter(fn ($t) => $t->eisenhowerQuadrant() === 'eliminate')->values(),
            ];

            $areas = $user->lifeAreas()->where('is_active', true)->get();
            $projects = $user->projects()->whereNotIn('status', ['completed', 'archived'])->get();
            $goals = $user->goals()->whereNotIn('status', ['completed', 'abandoned'])->get();

            return view('tasks.matrix', compact('matrix', 'view', 'areas', 'projects', 'goals'));
        }

        $tasks = match ($view) {
            'today' => $query->where(function ($q) {
                $q->where('due_date', today())
                  ->orWhere(function ($q2) {
                      $q2->where('is_recurring', true)->where('status', '!=', 'completed');
                  });
            })->where('status', '!=', 'completed')->orderBy('priority', 'desc')->get(),

            'overdue' => $query->where('due_date', '<', today())
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'cancelled')
                ->orderBy('due_date')->get(),

            default => $query->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'in_progress' THEN 1 WHEN 'completed' THEN 2 WHEN 'cancelled' THEN 3 END")
                ->latest()->get(),
        };

        $areas = $user->lifeAreas()->where('is_active', true)->get();
        $projects = $user->projects()->whereNotIn('status', ['completed', 'archived'])->get();
        $goals = $user->goals()->whereNotIn('status', ['completed', 'abandoned'])->get();

        return view('tasks.index', compact('tasks', 'view', 'areas', 'projects', 'goals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'effort' => 'required|in:small,medium,large',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'is_important' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'goal_id' => 'nullable|exists:goals,id',
            'is_recurring' => 'sometimes|boolean',
            'recurrence_rule' => 'nullable|string|max:255',
        ]);

        $validated['priority'] = $validated['priority'] ?? 'medium';
        $validated['is_important'] = $request->boolean('is_important');

        $request->user()->tasks()->create($validated);

        return back()->with('success', 'Task created.');
    }

    public function show(Request $request, Task $task)
    {
        $this->authorize($task);
        $task->load(['project', 'goal', 'subtasks']);

        return view('tasks.show', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize($task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'effort' => 'required|in:small,medium,large',
            'priority' => 'required|in:low,medium,high,urgent',
            'is_important' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'goal_id' => 'nullable|exists:goals,id',
            'is_recurring' => 'sometimes|boolean',
            'recurrence_rule' => 'nullable|string|max:255',
        ]);

        $validated['is_important'] = $request->boolean('is_important');
        $task->update($validated);

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorize($task);
        $task->delete();

        return back()->with('success', 'Task deleted.');
    }

    public function complete(Request $request, Task $task)
    {
        $this->authorize($task);

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Fire event — triggers XP award, streak update, achievement check
        event(new TaskCompletedEvent($task));
        $task->refresh();

        if ($request->wantsJson()) {
            $lifeArea = $task->resolveLifeArea();
            return response()->json([
                'success' => true,
                'message' => 'Task completed!',
                'xp' => $task->xp_awarded,
                'stat' => $lifeArea?->primary_stat ?? 'discipline',
            ]);
        }

        return back()->with('success', "Task completed! +{$task->xp_awarded} XP");
    }

    public function reopen(Request $request, Task $task)
    {
        $this->authorize($task);

        // Revoke XP before reopening
        if ($task->xp_awarded > 0) {
            app(XpService::class)->revokeTaskXp($task);
        }

        $task->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        return back()->with('success', 'Task reopened. XP has been returned.');
    }

    private function authorize(Task $task): void
    {
        abort_unless($task->user_id === auth()->id(), 403);
    }
}
