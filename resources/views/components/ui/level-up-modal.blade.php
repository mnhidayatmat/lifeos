{{-- Level-up celebration modal — shown via session flash --}}
@if(session('level_up'))
@php $levelUp = session('level_up'); @endphp
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
     @click="show = false"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl dark:shadow-black/40 p-8 max-w-sm w-full text-center transform border border-transparent dark:border-gray-800">
        {{-- Glow effect --}}
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 mx-auto mb-4 flex items-center justify-center shadow-lg shadow-indigo-200 dark:shadow-indigo-900/50">
            <span class="text-3xl font-bold text-white">{{ $levelUp['level'] }}</span>
        </div>

        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Level Up!</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">You've reached Level {{ $levelUp['level'] }}</p>

        @if(isset($levelUp['rank_changed']) && $levelUp['rank_changed'])
            <div class="mb-3">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">New Rank</p>
                <x-ui.rank-badge :rank="$levelUp['rank']" size="md" />
            </div>
        @endif

        <p class="text-xs text-gray-400 dark:text-gray-500">Click to dismiss</p>
    </div>
</div>
@endif
