<?php

namespace App\Http\Controllers;

use App\Models\ImportantDate;
use Illuminate\Http\Request;

class ImportantDateController extends Controller
{
    /** Buckets in display order. */
    private const BUCKETS = ['overdue', 'today', 'this_week', 'this_month', 'later', 'completed'];

    public function index(Request $request)
    {
        $user = $request->user();

        $dates = $user->importantDates()->with('lifeArea')->get()
            ->sortBy(fn (ImportantDate $d) => $d->nextOccurrence()->timestamp)
            ->values();

        $grouped = collect(self::BUCKETS)
            ->mapWithKeys(fn ($bucket) => [$bucket => $dates->filter(fn ($d) => $d->bucket() === $bucket)->values()]);

        $areas = $user->lifeAreas()->where('is_active', true)->get();

        $feedUrl = route('calendar.feed', ['token' => $user->calendarToken()]);

        $upcomingCount = $dates->filter(fn ($d) => ! $d->isCompleted())->count();

        return view('important-dates.index', compact('grouped', 'dates', 'areas', 'feedUrl', 'upcomingCount'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateDate($request);

        $request->user()->importantDates()->create($validated);

        return back()->with('success', 'Important date added.');
    }

    public function update(Request $request, ImportantDate $importantDate)
    {
        $this->authorizeOwner($importantDate);

        $validated = $this->validateDate($request);

        $importantDate->update($validated);

        return back()->with('success', 'Important date updated.');
    }

    public function destroy(Request $request, ImportantDate $importantDate)
    {
        $this->authorizeOwner($importantDate);
        $importantDate->delete();

        return back()->with('success', 'Important date deleted.');
    }

    public function toggle(Request $request, ImportantDate $importantDate)
    {
        $this->authorizeOwner($importantDate);

        $importantDate->update([
            'completed_at' => $importantDate->isCompleted() ? null : now(),
        ]);

        return back()->with('success', $importantDate->isCompleted() ? 'Marked as done.' : 'Reopened.');
    }

    private function validateDate(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'reminders' => 'nullable|array',
            'reminders.*' => 'integer|min:0|max:365',
            'recurrence' => 'nullable|in:yearly,monthly',
            'life_area_id' => 'nullable|exists:life_areas,id',
        ]);

        $validated['all_day'] = empty($validated['time']);
        $validated['reminders'] = array_values(array_unique(array_map('intval', $validated['reminders'] ?? [])));

        return $validated;
    }

    private function authorizeOwner(ImportantDate $importantDate): void
    {
        abort_unless($importantDate->user_id === auth()->id(), 403);
    }
}
