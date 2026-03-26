<?php

namespace App\Http\Controllers;

use App\Events\ReviewCompleted;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function daily(Request $request)
    {
        $user = $request->user();
        $today = today();

        $existing = $user->reviews()->where('type', 'daily')->where('period_date', $today)->first();
        $autoData = $this->reviewService->generateDailyData($user, $today);

        return view('reviews.daily', compact('existing', 'autoData'));
    }

    public function submitDaily(Request $request)
    {
        $validated = $request->validate([
            'completed_today' => 'nullable|string|max:1000',
            'mattered_most' => 'nullable|string|max:1000',
            'focus_tomorrow' => 'nullable|string|max:1000',
            'momentum' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $today = today();

        $review = $user->reviews()->updateOrCreate(
            ['type' => 'daily', 'period_date' => $today],
            [
                'responses' => $validated,
                'auto_summary' => $this->reviewService->generateDailyData($user, $today),
                'notes' => $validated['notes'] ?? null,
                'completed_at' => now(),
            ]
        );

        event(new ReviewCompleted($review));

        return redirect()->route('reviews.daily')->with('success', "Daily review completed! +{$review->xp_awarded} XP");
    }

    public function weekly(Request $request)
    {
        $user = $request->user();
        $weekStart = now()->startOfWeek();

        $existing = $user->reviews()->where('type', 'weekly')->where('period_date', $weekStart->toDateString())->first();
        $autoData = $this->reviewService->generateWeeklyData($user, $weekStart);

        return view('reviews.weekly', compact('existing', 'autoData'));
    }

    public function submitWeekly(Request $request)
    {
        $validated = $request->validate([
            'went_well' => 'nullable|string|max:1000',
            'got_stuck' => 'nullable|string|max:1000',
            'focus_next_week' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $weekStart = now()->startOfWeek();

        $review = $user->reviews()->updateOrCreate(
            ['type' => 'weekly', 'period_date' => $weekStart->toDateString()],
            [
                'responses' => $validated,
                'auto_summary' => $this->reviewService->generateWeeklyData($user, $weekStart),
                'notes' => $validated['notes'] ?? null,
                'completed_at' => now(),
            ]
        );

        event(new ReviewCompleted($review));

        return redirect()->route('reviews.weekly')->with('success', "Weekly review completed! +{$review->xp_awarded} XP");
    }

    public function monthly(Request $request)
    {
        $user = $request->user();
        $monthStart = now()->startOfMonth();

        $existing = $user->reviews()->where('type', 'monthly')->where('period_date', $monthStart->toDateString())->first();
        $autoData = $this->reviewService->generateMonthlyData($user, $monthStart);

        return view('reviews.monthly', compact('existing', 'autoData'));
    }

    public function submitMonthly(Request $request)
    {
        $validated = $request->validate([
            'biggest_win' => 'nullable|string|max:1000',
            'biggest_lesson' => 'nullable|string|max:1000',
            'focus_next_month' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $monthStart = now()->startOfMonth();

        $review = $user->reviews()->updateOrCreate(
            ['type' => 'monthly', 'period_date' => $monthStart->toDateString()],
            [
                'responses' => $validated,
                'auto_summary' => $this->reviewService->generateMonthlyData($user, $monthStart),
                'notes' => $validated['notes'] ?? null,
                'completed_at' => now(),
            ]
        );

        event(new ReviewCompleted($review));

        return redirect()->route('reviews.monthly')->with('success', "Monthly review completed! +{$review->xp_awarded} XP");
    }

    public function history(Request $request)
    {
        $reviews = $request->user()->reviews()
            ->whereNotNull('completed_at')
            ->latest('period_date')
            ->paginate(20);

        return view('reviews.history', compact('reviews'));
    }
}
