<x-app-layout>
    <x-slot name="header">Weekly Review</x-slot>
    <x-slot name="title">Weekly Review</x-slot>

    <div class="max-w-2xl">
        {{-- Auto summary --}}
        <x-ui.card class="mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">This Week's Summary</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-900">{{ $autoData['tasks_completed'] }}</p>
                    <p class="text-xs text-gray-500">Completed</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold {{ $autoData['overdue_tasks'] > 0 ? 'text-rose-500' : 'text-gray-900' }}">{{ $autoData['overdue_tasks'] }}</p>
                    <p class="text-xs text-gray-500">Overdue</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-indigo-600">{{ $autoData['xp_earned'] }}</p>
                    <p class="text-xs text-gray-500">XP Earned</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-900">{{ $autoData['current_streak'] }}</p>
                    <p class="text-xs text-gray-500">Day Streak</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                @if($autoData['strongest_area'])
                    <div class="p-3 bg-emerald-50 rounded-lg">
                        <p class="text-xs text-emerald-600 font-medium">Strongest Area</p>
                        <p class="font-semibold text-emerald-800">{{ $autoData['strongest_area'] }}</p>
                    </div>
                @endif
                @if($autoData['neglected_area'])
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <p class="text-xs text-amber-600 font-medium">Needs Attention</p>
                        <p class="font-semibold text-amber-800">{{ $autoData['neglected_area'] }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>

        @if($existing?->isCompleted())
            <x-ui.card>
                <div class="text-center py-6">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">This week's review is complete</h3>
                    <p class="text-xs text-gray-500 mt-1">See you next week for a fresh review.</p>
                </div>
            </x-ui.card>
        @else
            <x-ui.card>
                <form method="POST" action="{{ route('reviews.submit-weekly') }}">
                    @csrf
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Reflect on This Week</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">What went well this week?</label>
                            <textarea name="went_well" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Wins, breakthroughs, progress..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">What got stuck?</label>
                            <textarea name="got_stuck" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Blockers, missed goals, challenges..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">What should you focus on next week?</label>
                            <textarea name="focus_next_week" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Top priorities for the coming week..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            Complete Review (+25 XP)
                        </button>
                    </div>
                </form>
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
