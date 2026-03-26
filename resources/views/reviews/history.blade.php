<x-app-layout>
    <x-slot name="header">Review History</x-slot>
    <x-slot name="title">Review History</x-slot>

    <div class="max-w-2xl">
        @if($reviews->isEmpty())
            <x-ui.card>
                <x-ui.empty-state
                    icon="book-open"
                    title="No reviews yet"
                    description="Complete your first daily or weekly review to start tracking your reflections."
                />
            </x-ui.card>
        @else
            <div class="space-y-3">
                @foreach($reviews as $review)
                    <x-ui.card>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <x-ui.badge :color="$review->type === 'weekly' ? 'purple' : 'blue'" size="xs">
                                        {{ ucfirst($review->type) }}
                                    </x-ui.badge>
                                    <span class="text-sm font-medium text-gray-900">{{ $review->period_date->format('M j, Y') }}</span>
                                </div>
                                @if($review->auto_summary)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $review->auto_summary['tasks_completed'] ?? 0 }} tasks completed
                                        &middot; {{ $review->auto_summary['xp_earned'] ?? 0 }} XP earned
                                    </p>
                                @endif
                            </div>
                            <span class="text-xs font-medium text-indigo-600">+{{ $review->xp_awarded }} XP</span>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
