<x-app-layout>
    <x-slot name="header">Milestones</x-slot>
    <x-slot name="title">Milestones</x-slot>

    <div class="max-w-3xl space-y-6">
        {{-- Header --}}
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Milestones</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Meaningful moments as you build momentum. No points, no levels — just progress worth marking.</p>
            </div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 tabular-nums shrink-0">{{ $unlockedCount }} / {{ $totalCount }}</span>
        </div>

        {{-- Progress bar --}}
        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
            <div class="h-1.5 rounded-full bg-teal-500 transition-all" style="width: {{ $totalCount > 0 ? ($unlockedCount / $totalCount) * 100 : 0 }}%"></div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($milestones as $milestone)
                @php $isUnlocked = $unlocked->has($milestone->id); @endphp
                <div class="flex items-start gap-3 rounded-xl border p-4 transition-colors
                    {{ $isUnlocked
                        ? 'border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900'
                        : 'border-gray-100 dark:border-gray-800/60 bg-gray-50/60 dark:bg-gray-900/40' }}">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                        {{ $isUnlocked
                            ? 'bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600' }}">
                        @if($isUnlocked)
                            <x-icon :name="$milestone->icon ?? 'check-square'" class="w-5 h-5" />
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $isUnlocked ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $milestone->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $milestone->description }}</p>
                        @if($isUnlocked)
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1.5 font-medium">
                                Reached {{ $unlocked->get($milestone->id)->unlocked_at?->format('M j, Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
