<x-app-layout>
    <x-slot name="header">Analytics</x-slot>
    <x-slot name="title">Analytics</x-slot>

    <div class="space-y-6">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tasks this week</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ number_format($tasksThisWeek) }}</p>
            </x-ui.card>

            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tasks this month</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ number_format($tasksThisMonth) }}</p>
            </x-ui.card>

            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Current streak</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">
                    {{ $currentStreak }}
                    <span class="text-sm font-normal text-gray-400 dark:text-gray-500">day{{ $currentStreak === 1 ? '' : 's' }}</span>
                </p>
            </x-ui.card>

            <x-ui.card>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Longest streak</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">
                    {{ $longestStreak }}
                    <span class="text-sm font-normal text-gray-400 dark:text-gray-500">day{{ $longestStreak === 1 ? '' : 's' }}</span>
                </p>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Tasks Completed Trend (14 days) --}}
            <x-ui.card>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">Tasks completed</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Last 14 days</p>

                @php
                    $maxCount = max(array_column($completionTrend, 'count') ?: [1]);
                    if ($maxCount === 0) $maxCount = 1;
                @endphp

                <div class="flex items-end gap-1.5" style="height: 160px;">
                    @foreach($completionTrend as $day)
                        @php $heightPercent = ($day['count'] / $maxCount) * 100; @endphp
                        <div class="flex-1 flex flex-col items-center justify-end h-full">
                            <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400 mb-1">
                                {{ $day['count'] > 0 ? $day['count'] : '' }}
                            </span>
                            <div
                                class="w-full rounded-t bg-indigo-500 dark:bg-indigo-400 transition-all"
                                style="height: {{ max($heightPercent, 2) }}%; min-height: 2px;"
                            ></div>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-1.5 mt-2">
                    @foreach($completionTrend as $day)
                        <div class="flex-1 text-center">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $day['date'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            {{-- Task Completion Rate (7 days) --}}
            <x-ui.card>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">Completion rate</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Tasks done vs. due, last 7 days</p>

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
                                {{ $day['total'] > 0 ? $rate . '%' : '—' }}
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
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">Life area balance</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Tasks completed this month</p>

                @if(empty($areaBalance))
                    <p class="text-sm text-gray-400 dark:text-gray-500">No life area data yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($areaBalance as $area)
                            @php $widthPercent = ($area['count'] / $areaBalanceMax) * 100; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $area['color'] }}"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $area['name'] }}</span>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 tabular-nums">{{ $area['count'] }}</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div
                                        class="h-2.5 rounded-full transition-all"
                                        style="width: {{ max($widthPercent, 2) }}%; background-color: {{ $area['color'] }}"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- Totals & Milestones --}}
            <x-ui.card>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">All time</h3>

                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-3 text-center">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ number_format($totalTasks) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tasks</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-3 text-center">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ number_format($totalGoals) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Goals</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-3 text-center">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ number_format($totalProjects) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Projects</p>
                    </div>
                </div>

                <a href="{{ route('milestones.index') }}" class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 group">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L23 12l-5.714 2.143L15 21l-2.286-6.857L7 12l5.714-2.143L15 3z"/></svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Milestones reached</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">{{ $milestonesUnlocked }} / {{ $milestonesTotal }}</span>
                </a>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
