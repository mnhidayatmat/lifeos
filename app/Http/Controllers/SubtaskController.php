<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task)
    {
        abort_unless($task->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $task->subtasks()->create([
            ...$validated,
            'sort_order' => $task->subtasks()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Subtask added.');
    }

    public function toggle(Request $request, Subtask $subtask)
    {
        abort_unless($subtask->task->user_id === auth()->id(), 403);

        $subtask->update([
            'is_completed' => !$subtask->is_completed,
            'completed_at' => !$subtask->is_completed ? now() : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'completed' => $subtask->is_completed]);
        }

        return back();
    }

    public function destroy(Request $request, Subtask $subtask)
    {
        abort_unless($subtask->task->user_id === auth()->id(), 403);

        $subtask->delete();

        return back()->with('success', 'Subtask removed.');
    }
}
