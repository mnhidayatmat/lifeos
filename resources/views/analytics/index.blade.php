<x-app-layout>
    <x-slot name="header">Analytics</x-slot>
    <x-slot name="title">Analytics</x-slot>

    <div class="space-y-6">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tasks Completed</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalTasks) }}</p>
            </x-ui.card>

            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Goals Completed</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalGoals) }}</p>
            </x-ui.card>

            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Current Streak</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $currentStreak }}
                    <span class="text-sm font-normal text-gray-400 dark:text-gray-500">days</span>
                </p>
            </x-ui.card>

            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Longest Streak</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $longestStreak }}
                    <span class="text-sm font-normal text-gray-400 dark:text-gray-500">days</span>
                </p>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- XP Trend (14 days) --}}
            <x-ui.card>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">XP Trend</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Last 14 days</p>

                @php
                    $maxXp = max(array_column($xpTrend, 'xp') ?: [1]);
                    if ($maxXp === 0) $maxXp = 1;
                @endphp

                <div class="flex items-end gap-1.5" style="height: 160px;">
                    @foreach($xpTrend as $day)
                        @php
                            $heightPercent = ($day['xp'] / $maxXp) * 100;
                        @endphp
                        <div class="flex-1 flex flex-col items-center justify-end h-full">
                            <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400 mb-1">
                                {{ $day['xp'] > 0 ? $day['xp'] : '' }}
                            </span>
                            <div
                                class="w-full rounded-t bg-indigo-500 dark:bg-indigo-400 transition-all"
                                style="height: {{ max($heightPercent, 2) }}%; min-height: 2px;"
                            ></div>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-1.5 mt-2">
                    @foreach($xpTrend as $day)
                        <div class="flex-1 text-center">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $day['date'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            {{-- Task Completion Rate (7 days) --}}
            <x-ui.card>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Task Completion Rate</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Last 7 days</p>

                <div class="flex items-end gap-3" style="height: 160px;">
                    @foreach($completionRate as $day)
                        @php
                            $rate = $day['rate'];
                            if ($rate > 70) {
                                $barColor = 'bg-emerald-500 dark:bg-emerald-400';
                            } elseif ($rate > 40) {
                                $barColor = 'bg-amber-500 dark:bg-amber-400';
                            } else {
                                $barColor = 'bg-rose-500 dark:bg-rose-400';
                            }
                        @endphp
                        <div class="flex-1 flex flex-col items-center justify-end h-full">
                            <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400 mb-1">
                                {{ $rate }}%
                            </span>
                            <div
                                class="w-full rounded-t {{ $barColor }} transition-all"
                                style="height: {{ max($rate, 2) }}%; min-height: 2px;"
                            ></div>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-3 mt-2">
                    @foreach($completionRate as $day)
                        <div class="flex-1 text-center">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $day['date'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">&gt;70%</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">40-70%</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">&lt;40%</span>
                    </div>
                </div>
            </x-ui.card>

            {{-- Life Area Balance --}}
            <x-ui.card>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Life Area Balance</h3>

                @php
                    $maxCount = max(array_column($areaBalance, 'count') ?: [1]);
                    if ($maxCount === 0) $maxCount = 1;
                @endphp

                @if(empty($areaBalance))
                    <p class="text-sm text-gray-400 dark:text-gray-500">No life area data yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($areaBalance as $area)
                            @php
                                $widthPercent = ($area['count'] / $maxCount) * 100;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $area['color'] }}"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $area['name'] }}</span>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $area['count'] }}</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div
                                        class="h-2.5 rounded-full transition-all"
                                        style="width: {{ $widthPercent }}%; background-color: {{ $area['color'] }}"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- Stat Growth --}}
            <x-ui.card>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Stat Growth</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">This week vs last week</p>

                @if(empty($statGrowth))
                    <p class="text-sm text-gray-400 dark:text-gray-500">No stat data yet.</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($statGrowth as $stat => $values)
                            @php
                                $thisWeek = $values['this_week'];
                                $lastWeek = $values['last_week'];
                                $diff = $thisWeek - $lastWeek;
                            @endphp
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $stat }}</span>

                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="text-gray-400 dark:text-gray-500">
                                            Last: <span class="font-semibold text-gray-500 dark:text-gray-400">{{ $lastWeek }}</span>
                                        </span>
                                        <span class="text-gray-600 dark:text-gray-300">
                                            Now: <span class="font-semibold">{{ $thisWeek }}</span>
                                        </span>
                                    </div>

                                    @if($diff > 0)
                                        <div class="flex items-center gap-0.5">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">+{{ $diff }}</span>
                                        </div>
                                    @elseif($diff < 0)
                                        <div class="flex items-center gap-0.5">
                                            <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                            <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $diff }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-0.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">0</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
