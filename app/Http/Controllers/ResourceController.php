<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Task;
use App\Services\XpService;
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

        // Award XP if created as completed
        if ($validated['status'] === 'completed') {
            $this->awardResourceXp($resource, $request->user());
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

        $resource->update($validated);

        // Award XP on status change to completed
        if ($oldStatus !== 'completed' && $validated['status'] === 'completed') {
            $this->awardResourceXp($resource, $request->user());
        }

        return back()->with('success', 'Resource updated.');
    }

    public function destroy(Request $request, Resource $resource)
    {
        abort_unless($resource->user_id === auth()->id(), 403);
        $resource->delete();

        return back()->with('success', 'Resource removed.');
    }

    private function awardResourceXp(Resource $resource, $user): void
    {
        $xp = 15; // Medium XP for completing a resource
        $resource->update(['xp_awarded' => $xp, 'completed_at' => now()]);

        $lifeArea = $resource->lifeArea;
        if ($lifeArea) {
            $primaryXp = (int) floor($xp * 0.7);
            $secondaryXp = $xp - $primaryXp;
            $user->stats()->where('stat', $lifeArea->primary_stat)->increment('total_xp', $primaryXp);
            $user->stats()->where('stat', $lifeArea->secondary_stat)->increment('total_xp', $secondaryXp);
        } else {
            $user->stats()->where('stat', 'knowledge')->increment('total_xp', $xp);
        }

        $user->increment('total_xp', $xp);
        $user->xpLogs()->create(['source_type' => 'resource', 'source_id' => $resource->id, 'xp_amount' => $xp, 'stat' => $lifeArea?->primary_stat ?? 'knowledge', 'description' => "Completed: {$resource->title}", 'created_at' => now()]);
    }
}
