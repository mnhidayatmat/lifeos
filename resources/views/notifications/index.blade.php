<x-app-layout>
    <x-slot name="header">Notifications</x-slot>
    <x-slot name="title">Notifications</x-slot>

    <div class="max-w-2xl">
        @if($notifications->isNotEmpty())
            <div class="flex justify-end mb-4">
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-700">Mark all as read</button>
                </form>
            </div>
        @endif

        @if($notifications->isEmpty())
            <x-ui.card>
                <x-ui.empty-state
                    icon="check-square"
                    title="No notifications"
                    description="You're all caught up. Notifications appear here when you level up or unlock achievements."
                />
            </x-ui.card>
        @else
            <div class="space-y-2">
                @foreach($notifications as $notification)
                    <x-ui.card class="{{ $notification->read_at ? 'opacity-60' : '' }}">
                        <div class="flex items-start gap-3">
                            @php $type = $notification->data['type'] ?? 'info'; @endphp
                            <div class="w-8 h-8 rounded-lg {{ $type === 'level_up' ? 'bg-indigo-100' : ($type === 'achievement' ? 'bg-amber-100' : 'bg-gray-100') }} flex items-center justify-center shrink-0">
                                <x-icon :name="$type === 'achievement' ? 'trophy' : 'user'" class="w-4 h-4 {{ $type === 'level_up' ? 'text-indigo-600' : ($type === 'achievement' ? 'text-amber-600' : 'text-gray-500') }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @unless($notification->read_at)
                                <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">Dismiss</button>
                                </form>
                            @endunless
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
            <div class="mt-6">{{ $notifications->links() }}</div>
        @endif
    </div>
</x-app-layout>
