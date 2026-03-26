<?php

namespace App\Http\Controllers;

use App\Services\OnboardingService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(private OnboardingService $onboardingService) {}

    public function index(Request $request)
    {
        if ($request->user()->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.archetype');
    }

    public function storeArchetype(Request $request)
    {
        $validated = $request->validate([
            'archetype' => 'required|in:student,researcher,founder,professional,creator',
        ]);

        $user = $request->user();
        $user->update(['archetype' => $validated['archetype']]);

        // Seed life areas
        $this->onboardingService->seedAreasForArchetype($user, $validated['archetype']);

        return redirect()->route('onboarding.areas');
    }

    public function areas(Request $request)
    {
        if ($request->user()->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        $areas = $request->user()->lifeAreas()->get();

        return view('onboarding.areas', compact('areas'));
    }

    public function storeAreas(Request $request)
    {
        // Just confirm and proceed — areas were already seeded
        return redirect()->route('onboarding.first-goal');
    }

    public function firstGoal(Request $request)
    {
        if ($request->user()->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        $areas = $request->user()->lifeAreas()->where('is_active', true)->get();

        return view('onboarding.first-goal', compact('areas'));
    }

    public function storeFirstGoal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'life_area_id' => 'required|exists:life_areas,id',
        ]);

        $request->user()->goals()->create([
            ...$validated,
            'status' => 'in_progress',
        ]);

        // Complete onboarding
        $this->onboardingService->completeOnboarding($request->user());

        return redirect()->route('onboarding.welcome');
    }

    public function welcome(Request $request)
    {
        return view('onboarding.welcome');
    }
}
