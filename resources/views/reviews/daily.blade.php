<x-app-layout>
    <x-slot name="header">Daily Review</x-slot>
    <x-slot name="title">Daily Review</x-slot>

    <div class="max-w-2xl">
        {{-- Auto summary --}}
        <x-ui.card class="mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Today's Summary</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $autoData['tasks_completed'] }}</p>
                    <p class="text-xs text-gray-500">Tasks Done</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-indigo-600">{{ $autoData['xp_earned'] }}</p>
                    <p class="text-xs text-gray-500">XP Earned</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ count($autoData['stat_gains']) }}</p>
                    <p class="text-xs text-gray-500">Stats Improved</p>
                </div>
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
                    <h3 class="text-sm font-semibold text-gray-900">Today's review is complete</h3>
                    <p class="text-xs text-gray-500 mt-1">Come back tomorrow for your next check-in.</p>
                </div>
            </x-ui.card>
        @else
            <x-ui.card>
                <form method="POST" action="{{ route('reviews.submit-daily') }}">
                    @csrf
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Reflect on Today</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">What did you complete today?</label>
                            <textarea name="completed_today" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Key accomplishments..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">What mattered most today?</label>
                            <textarea name="mattered_most" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="The most impactful thing..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">What is your top focus tomorrow?</label>
                            <textarea name="focus_tomorrow" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Main priority for tomorrow..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">How is your momentum today?</label>
                            <div class="flex gap-2">
                                @foreach(range(1, 5) as $i)
                                    <label class="flex-1">
                                        <input type="radio" name="momentum" value="{{ $i }}" class="peer sr-only" @checked($i === 3)>
                                        <div class="text-center py-2 rounded-lg border-2 border-gray-200 text-sm cursor-pointer
                                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700
                                                    hover:border-gray-300 transition-colors">
                                            {{ $i }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-400 mt-1 px-1">
                                <span>Low</span>
                                <span>High</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Anything else on your mind..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            Complete Review (+10 XP)
                        </button>
                    </div>
                </form>
            </x-ui.card>
        @endif

        <div class="mt-4 text-center">
            <a href="{{ route('reviews.history') }}" class="text-xs text-gray-400 hover:text-gray-600">View past reviews</a>
        </div>
    </div>
</x-app-layout>
