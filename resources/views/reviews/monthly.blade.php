<x-app-layout>
    <x-slot name="header">Monthly Review</x-slot>
    <x-slot name="title">Monthly Review</x-slot>

    <div class="max-w-2xl">
        {{-- Navigation --}}
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('reviews.daily') }}" class="px-3 py-1.5 text-sm rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">Daily</a>
            <a href="{{ route('reviews.weekly') }}" class="px-3 py-1.5 text-sm rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">Weekly</a>
            <a href="{{ route('reviews.monthly') }}" class="px-3 py-1.5 text-sm rounded-lg bg-indigo-50 text-indigo-700 font-medium dark:bg-indigo-950 dark:text-indigo-400">Monthly</a>
            <a href="{{ route('reviews.history') }}" class="px-3 py-1.5 text-sm rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">History</a>
        </div>

        {{-- Auto summary --}}
        <x-ui.card class="mb-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ now()->format('F Y') }} Summary</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $autoData['tasks_completed'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tasks Done</p>
                </div>
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-xl font-bold text-emerald-600">{{ $autoData['goals_completed'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Goals Completed</p>
                </div>
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-xl font-bold text-indigo-600">{{ number_format($autoData['total_xp']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">XP Earned</p>
                </div>
                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $autoData['reviews_completed'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Reviews Done</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                @if($autoData['best_week'])
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/30 rounded-lg">
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Best Week</p>
                        <p class="text-sm font-semibold text-indigo-800 dark:text-indigo-300">{{ $autoData['best_week'] }} ({{ $autoData['best_week_xp'] }} XP)</p>
                    </div>
                @endif
                @if($autoData['level_now'] > $autoData['level_start'])
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 rounded-lg">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Level Growth</p>
                        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ $autoData['level_start'] }} &rarr; {{ $autoData['level_now'] }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>

        @if($existing?->isCompleted())
            <x-ui.card>
                <div class="text-center py-6">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">This month's review is complete</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">See you next month.</p>
                </div>
            </x-ui.card>
        @else
            <x-ui.card>
                <form method="POST" action="{{ route('reviews.submit-monthly') }}">
                    @csrf
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Reflect on This Month</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">What was your biggest win this month?</label>
                            <textarea name="biggest_win" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Your proudest accomplishment..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">What was the biggest lesson learned?</label>
                            <textarea name="biggest_lesson" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="What would you do differently?"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">What should you focus on next month?</label>
                            <textarea name="focus_next_month" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Top priorities for the coming month..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            Complete Review (+25 XP)
                        </button>
                    </div>
                </form>
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
