<?php

namespace App\Http\Controllers;

use App\Models\IdentityTrait;
use App\Models\User;
use Illuminate\Http\Request;

class VisionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vision = $user->vision;
        $traits = $user->identityTraits;

        return view('vision.index', compact('vision', 'traits'));
    }

    public function updateVision(Request $request)
    {
        $validated = $request->validate([
            'vision_statement' => 'nullable|string|max:2000',
            'anti_vision' => 'nullable|string|max:2000',
        ]);

        $request->user()->vision()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return back()->with('success', 'Vision updated.');
    }

    public function updateStatements(Request $request)
    {
        $validated = $request->validate([
            'statements' => 'nullable|string|max:2000',
        ]);

        $statements = array_filter(array_map('trim', explode("\n", $validated['statements'] ?? '')));

        $request->user()->vision()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['i_am_statements' => $statements]
        );

        return back()->with('success', 'I Am statements updated.');
    }

    public function storeTrait(Request $request)
    {
        $validated = $request->validate([
            'trait' => 'required|string|max:255',
            'linked_stat' => 'nullable|in:' . implode(',', User::STATS),
            'status' => 'required|in:aspirational,developing,integrated',
        ]);

        $request->user()->identityTraits()->create($validated);

        return back()->with('success', 'Trait added.');
    }

    public function updateTrait(Request $request, IdentityTrait $identityTrait)
    {
        abort_unless($identityTrait->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'status' => 'required|in:aspirational,developing,integrated',
        ]);

        $identityTrait->update($validated);

        return back()->with('success', 'Trait updated.');
    }

    public function destroyTrait(Request $request, IdentityTrait $identityTrait)
    {
        abort_unless($identityTrait->user_id === auth()->id(), 403);
        $identityTrait->delete();

        return back()->with('success', 'Trait removed.');
    }
}
