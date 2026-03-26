<x-app-layout>
    <x-slot name="header">Achievements</x-slot>
    <x-slot name="title">Achievements</x-slot>

    <div class="max-w-3xl">
        <p class="text-sm text-gray-500 mb-6">Unlock achievements by completing tasks, reaching milestones, and growing your character.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($allAchievements as $achievement)
                @php
                    $unlocked = in_array($achievement->id, $unlockedIds);
                    $userAchievement = $userAchievements[$achievement->id] ?? null;
                @endphp
                <x-ui.card class="{{ $unlocked ? '' : 'opacity-50' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $unlocked ? 'bg-amber-100' : 'bg-gray-100' }} flex items-center justify-center shrink-0">
                            <x-icon :name="$achievement->icon ?? 'trophy'" class="w-5 h-5 {{ $unlocked ? 'text-amber-600' : 'text-gray-400' }}" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold {{ $unlocked ? 'text-gray-900' : 'text-gray-500' }}">{{ $achievement->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $achievement->description }}</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs font-medium {{ $unlocked ? 'text-indigo-600' : 'text-gray-400' }}">+{{ $achievement->xp_reward }} XP</span>
                                @if($unlocked && $userAchievement)
                                    <span class="text-xs text-gray-400">{{ $userAchievement->unlocked_at->format('M j, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    </div>
</x-app-layout>
