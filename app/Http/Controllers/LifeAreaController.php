<?php

namespace App\Http\Controllers;

use App\Models\LifeArea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LifeAreaController extends Controller
{
    public function index(Request $request)
    {
        $areas = $request->user()->lifeAreas()->get();

        return view('life-areas.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'primary_stat' => 'required|string|in:' . implode(',', User::STATS),
            'secondary_stat' => 'required|string|in:' . implode(',', User::STATS),
        ]);

        $user = $request->user();

        if ($user->lifeAreas()->count() >= 10) {
            return back()->withErrors(['name' => 'You can have a maximum of 10 life areas.']);
        }

        $user->lifeAreas()->create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'sort_order' => $user->lifeAreas()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Life area created.');
    }

    public function update(Request $request, LifeArea $lifeArea)
    {
        $this->authorize($lifeArea);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'primary_stat' => 'required|string|in:' . implode(',', User::STATS),
            'secondary_stat' => 'required|string|in:' . implode(',', User::STATS),
        ]);

        $lifeArea->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        return back()->with('success', 'Life area updated.');
    }

    public function destroy(Request $request, LifeArea $lifeArea)
    {
        $this->authorize($lifeArea);

        $lifeArea->delete();

        return back()->with('success', 'Life area deleted.');
    }

    public function toggle(Request $request, LifeArea $lifeArea)
    {
        $this->authorize($lifeArea);

        $user = $request->user();

        if (!$lifeArea->is_active && $user->lifeAreas()->where('is_active', true)->count() >= 8) {
            return back()->withErrors(['toggle' => 'You can have a maximum of 8 active life areas.']);
        }

        $lifeArea->update(['is_active' => !$lifeArea->is_active]);

        return back()->with('success', $lifeArea->is_active ? 'Life area activated.' : 'Life area deactivated.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:life_areas,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            $request->user()->lifeAreas()->where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    private function authorize(LifeArea $lifeArea): void
    {
        abort_unless($lifeArea->user_id === auth()->id(), 403);
    }
}
