<x-app-layout>
    <x-slot name="header">Goals</x-slot>
    <x-slot name="title">Goals</x-slot>

    @php
        $statusColors = [
            'not_started' => 'gray',
            'in_progress' => 'blue',
            'on_hold'     => 'amber',
            'completed'   => 'emerald',
            'abandoned'   => 'gray',
        ];
        $priorityColors = [
            'low'    => 'text-gray-500 dark:text-gray-400',
            'medium' => 'text-blue-600 dark:text-blue-400',
            'high'   => 'text-amber-600 dark:text-amber-400',
            'urgent' => 'text-rose-600 dark:text-rose-400',
        ];

        $totalCount     = $goals->count();
        $activeCount    = $goals->where('status', 'in_progress')->count();
        $completedCount = $goals->where('status', 'completed')->count();
        $avgProgress    = $totalCount ? (int) round($goals->avg('progress')) : 0;
    @endphp

    <div class="max-w-4xl">

        {{-- Metrics strip --}}
        @if($totalCount)
            <div class="grid grid-cols-3 gap-2.5 sm:gap-3 mb-5">
                <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-3.5">
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white tabular-nums leading-none">{{ $activeCount }}</p>
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-1">In progress</p>
                </div>
                <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-3.5">
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white tabular-nums leading-none">{{ $completedCount }}</p>
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-1">Completed</p>
                </div>
                <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-3.5">
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white tabular-nums leading-none">{{ $avgProgress }}<span class="text-sm font-semibold text-gray-400">%</span></p>
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-1">Avg progress</p>
                </div>
            </div>
        @endif

        {{-- Filter tabs + desktop New Goal button --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex items-center gap-2 overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0 no-scrollbar flex-1">
                <a href="{{ route('goals.index') }}"
                   class="shrink-0 whitespace-nowrap px-3.5 py-2 text-sm rounded-full border transition-colors {{ !$areaFilter ? 'bg-teal-600 border-teal-600 text-white font-medium' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 active:bg-gray-50 dark:active:bg-gray-800' }}">
                    All
                </a>
                @foreach($areas as $area)
                    @php $isActive = $areaFilter == $area->id; @endphp
                    <a href="{{ route('goals.index', ['area' => $area->id]) }}"
                       class="shrink-0 inline-flex items-center gap-1.5 whitespace-nowrap px-3.5 py-2 text-sm rounded-full border transition-colors {{ $isActive ? 'font-medium text-white' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300 active:bg-gray-50 dark:active:bg-gray-800' }}"
                       @if($isActive) style="background-color: {{ $area->color }}; border-color: {{ $area->color }}" @endif>
                        <span class="w-2 h-2 rounded-full shrink-0 {{ $isActive ? 'bg-white/80' : '' }}" @if(!$isActive) style="background-color: {{ $area->color }}" @endif></span>
                        {{ $area->name }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('goals.create') }}"
               class="hidden sm:inline-flex shrink-0 items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-white bg-teal-600 rounded-full hover:bg-teal-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                New Goal
            </a>
        </div>

        {{-- Goals list --}}
        @if($goals->isEmpty())
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <x-ui.empty-state
                    icon="target"
                    title="{{ $areaFilter ? 'No goals in this area' : 'No goals yet' }}"
                    description="Set your first goal to give your tasks purpose and direction."
                    action="Create Goal"
                    :actionUrl="route('goals.create')"
                />
            </div>
        @else
            <div class="space-y-3">
                @foreach($goals as $goal)
                    @php
                        $isOverdue = $goal->due_date && $goal->due_date->isPast() && $goal->status !== 'completed';
                        $isDone    = $goal->status === 'completed';
                    @endphp
                    <a href="{{ route('goals.show', $goal) }}" class="block group">
                        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 pl-5 active:bg-gray-50 dark:active:bg-gray-800/50 group-hover:border-gray-300 dark:group-hover:border-gray-700 transition">
                            {{-- Life-area accent rail --}}
                            <span class="absolute left-0 inset-y-0 w-1.5" style="background-color: {{ $goal->lifeArea->color }}"></span>

                            {{-- Top row: area + status --}}
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium min-w-0" style="color: {{ $goal->lifeArea->color }}">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background-color: {{ $goal->lifeArea->color }}"></span>
                                    <span class="truncate">{{ $goal->lifeArea->name }}</span>
                                </span>
                                <x-ui.badge :color="$statusColors[$goal->status] ?? 'gray'" size="xs" class="shrink-0">
                                    {{ str_replace('_', ' ', ucfirst($goal->status)) }}
                                </x-ui.badge>
                            </div>

                            {{-- Title --}}
                            <div class="flex items-start gap-1.5 mb-2.5">
                                @if($goal->is_domino)
                                    <span class="text-amber-500 dark:text-amber-400 shrink-0 mt-0.5" title="Domino Goal — Your #1 Priority">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </span>
                                @endif
                                <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white leading-snug line-clamp-2 {{ $isDone ? 'line-through text-gray-400 dark:text-gray-500' : '' }}">{{ $goal->title }}</h3>
                            </div>

                            {{-- Meta: priority + due date --}}
                            <div class="flex items-center gap-3 text-xs mb-3">
                                <span class="inline-flex items-center gap-1 font-medium {{ $priorityColors[$goal->priority] ?? 'text-gray-500' }}">
                                    <x-icon name="flag" class="w-3.5 h-3.5" />
                                    {{ ucfirst($goal->priority) }}
                                </span>
                                @if($goal->due_date)
                                    <span class="inline-flex items-center gap-1 {{ $isOverdue ? 'text-rose-500 dark:text-rose-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                                        <x-icon name="calendar" class="w-3.5 h-3.5" />
                                        {{ $goal->due_date->format('M j, Y') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Progress --}}
                            <div class="flex items-center gap-3">
                                <x-ui.progress-bar :value="$goal->progress" :color="$isDone ? 'emerald' : 'teal'" size="sm" class="flex-1" />
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200 tabular-nums w-9 text-right">{{ $goal->progress }}%</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Floating action button — new goal (mobile). Desktop uses the header button. --}}
    <a href="{{ route('goals.create') }}" aria-label="New goal"
       class="sm:hidden fixed right-5 z-40 w-14 h-14 rounded-full bg-teal-600 text-white shadow-lg shadow-teal-900/30 flex items-center justify-center active:scale-95 active:bg-teal-700 transition"
       style="bottom: calc(env(safe-area-inset-bottom) + 5rem);">
        <x-icon name="plus" class="w-7 h-7" />
    </a>
</x-app-layout>
