<x-app-layout>
    <x-slot name="header">Profile</x-slot>
    <x-slot name="title">Profile</x-slot>

    <div class="max-w-4xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Profile Card --}}
            <div class="lg:col-span-1 space-y-6">
                <x-ui.card>
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-indigo-700">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h2>
                        @if($user->title)
                            <p class="text-sm text-gray-500">{{ $user->title }}</p>
                        @endif
                        <div class="mt-2">
                            <x-ui.rank-badge :rank="$user->rank" size="md" />
                        </div>
                        <p class="mt-3 text-3xl font-bold text-gray-900">Level {{ $user->level }}</p>

                        <div class="mt-4">
                            <x-ui.progress-bar :value="$xpProgress" :max="max($xpNeeded, 1)" color="indigo" size="md" :showLabel="true">
                                XP to next level
                            </x-ui.progress-bar>
                            <p class="mt-1 text-xs text-gray-500">{{ number_format($xpProgress) }} / {{ number_format($xpNeeded) }} XP</p>
                        </div>

                        <p class="mt-3 text-sm text-gray-400">Total XP: {{ number_format($user->total_xp) }}</p>
                    </div>
                </x-ui.card>

                {{-- Quick links --}}
                <x-ui.card>
                    <a href="{{ route('progression.achievements') }}" class="flex items-center justify-between text-sm text-gray-700 hover:text-indigo-600">
                        <span class="font-medium">Achievements</span>
                        <x-icon name="chevron-down" class="w-4 h-4 -rotate-90" />
                    </a>
                </x-ui.card>
            </div>

            {{-- Stats & Activity --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Stats --}}
                <x-ui.card>
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Character Stats</h3>
                    <div class="space-y-4">
                        @foreach(\App\Models\User::STATS as $stat)
                            @php $statXp = $stats[$stat] ?? 0; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($stat) }}</span>
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($statXp) }} XP</span>
                                </div>
                                <x-ui.stat-bar :stat="$stat" :value="min($statXp, 100)" />
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>

                {{-- Recent XP Activity --}}
                <x-ui.card>
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    @if($recentXpLogs->isEmpty())
                        <p class="text-sm text-gray-400">No XP earned yet. Complete a task to get started.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($recentXpLogs as $log)
                                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-stat-{{ $log->stat }}"></div>
                                        <div>
                                            <p class="text-sm text-gray-700">{{ $log->description }}</p>
                                            <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-indigo-600">+{{ $log->xp_amount }} XP</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
