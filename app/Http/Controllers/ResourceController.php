<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $statusFilter = $request->query('status');

        $query = $user->resources()->with('lifeArea');
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $resources = $query->latest()->get();
        $areas = $user->lifeAreas()->where('is_active', true)->get();

        return view('resources.index', compact('resources', 'areas', 'statusFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:book,article,podcast,course,video',
            'author' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:500',
            'life_area_id' => 'nullable|exists:life_areas,id',
            'status' => 'required|in:to_consume,in_progress,completed',
        ]);

        $resource = $request->user()->resources()->create($validated);

        if ($validated['status'] === 'completed') {
            $resource->update(['completed_at' => now()]);
        }

        return back()->with('success', 'Resource added.');
    }

    public function update(Request $request, Resource $resource)
    {
        abort_unless($resource->user_id === auth()->id(), 403);

        $oldStatus = $resource->status;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:book,article,podcast,course,video',
            'author' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:500',
            'life_area_id' => 'nullable|exists:life_areas,id',
            'status' => 'required|in:to_consume,in_progress,completed',
            'notes' => 'nullable|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        // Stamp completion time when moving into completed
        if ($oldStatus !== 'completed' && $validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $resource->update($validated);

        return back()->with('success', 'Resource updated.');
    }

    public function destroy(Request $request, Resource $resource)
    {
        abort_unless($resource->user_id === auth()->id(), 403);
        $resource->delete();

        return back()->with('success', 'Resource removed.');
    }
}
