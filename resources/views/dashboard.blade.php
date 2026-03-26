<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="title">Dashboard</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Domino Goal --}}
            @if($dominoGoal)
                <x-ui.card class="border-amber-200 dark:border-amber-900/50 bg-amber-50/30 dark:bg-amber-950/10">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wider">Your #1 Domino Goal</p>
                            <a href="{{ route('goals.show', $dominoGoal) }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-indigo-600">{{ $dominoGoal->title }}</a>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $dominoGoal->progress }}%</span>
                    </div>
                    <x-ui.progress-bar :value="$dominoGoal->progress" color="amber" size="xs" class="mt-2" />
                </x-ui.card>
            @endif

            {{-- WIP Warning --}}
            @if($activeProjectCount > 3)
                <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 p-3 text-sm text-amber-700 dark:text-amber-300 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <span>You have <strong>{{ $activeProjectCount }}</strong> active projects. Focus on finishing before starting new ones.</span>
                </div>
            @endif

            {{-- Today's Tasks --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Today's Tasks</h2>
                    <span class="text-xs text-gray-500">{{ now()->format('l, M j') }}</span>
                </div>

                @if($todayTasks->isEmpty())
                    <x-ui.empty-state
                        icon="check-square"
                        title="Nothing due today"
                        description="Add a task for today to get started."
                        action="Add Task"
                        :actionUrl="route('tasks.index')"
                    />
                @else
                    <div class="space-y-1">
                        @foreach($todayTasks as $task)
                            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                                <form method="POST" action="{{ route('tasks.complete', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-5 h-5 rounded border-2 border-gray-300 hover:border-indigo-400 flex items-center justify-center transition-colors"></button>
                                </form>
                                <a href="{{ route('tasks.show', $task) }}" class="flex-1 min-w-0">
                                    <span class="text-sm text-gray-900">{{ $task->title }}</span>
                                </a>
                                <x-ui.effort-badge :effort="$task->effort" />
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Quick add --}}
                <form method="POST" action="{{ route('tasks.store') }}" class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                    @csrf
                    <input type="hidden" name="due_date" value="{{ today()->format('Y-m-d') }}">
                    <input type="hidden" name="effort" value="medium">
                    <input type="text" name="title" placeholder="Quick add task for today..."
                           class="flex-1 rounded-lg border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white" required>
                    <button type="submit" class="px-3 py-2 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">Add</button>
                </form>
            </x-ui.card>

            {{-- Overdue Tasks --}}
            @if($overdueTasks->isNotEmpty())
                <x-ui.card>
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center justify-between w-full">
                            <h2 class="text-base font-semibold text-gray-900">Overdue</h2>
                            <div class="flex items-center gap-2">
                                <x-ui.badge color="rose">{{ $overdueTasks->count() }}</x-ui.badge>
                                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" ::class="open && 'rotate-180'" />
                            </div>
                        </button>
                        <div x-show="open" x-collapse class="mt-3 space-y-1">
                            @foreach($overdueTasks as $task)
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                                    <form method="POST" action="{{ route('tasks.complete', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-5 h-5 rounded border-2 border-rose-300 hover:border-rose-500 flex items-center justify-center transition-colors"></button>
                                    </form>
                                    <a href="{{ route('tasks.show', $task) }}" class="flex-1 min-w-0">
                                        <span class="text-sm text-gray-900">{{ $task->title }}</span>
                                    </a>
                                    <span class="text-xs text-rose-500">{{ $task->due_date->format('M j') }}</span>
                                    <x-ui.effort-badge :effort="$task->effort" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-ui.card>
            @endif

            {{-- Today's Habits --}}
            @if($todayHabits->isNotEmpty())
                <x-ui.card>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Habits</h2>
                        <a href="{{ route('habits.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">View all</a>
                    </div>
                    <div class="space-y-1">
                        @foreach($todayHabits as $habit)
                            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                                <form method="POST" action="{{ route('habits.toggle', $habit) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-5 h-5 rounded border-2 {{ $habit->isCompletedToday() ? 'bg-emerald-500 border-emerald-500' : 'border-gray-300 dark:border-gray-600 hover:border-emerald-400' }} flex items-center justify-center transition-colors">
                                        @if($habit->isCompletedToday())
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <span class="text-sm {{ $habit->isCompletedToday() ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-gray-100' }} flex-1">{{ $habit->title }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 capitalize">{{ $habit->routine }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            {{-- Active Goals --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Active Goals</h2>
                    <a href="{{ route('goals.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700">View all</a>
                </div>
                @if($activeGoals->isEmpty())
                    <x-ui.empty-state
                        icon="target"
                        title="No active goals"
                        description="Set a goal to give your work direction."
                        action="Create Goal"
                        :actionUrl="route('goals.create')"
                    />
                @else
                    <div class="space-y-4">
                        @foreach($activeGoals as $goal)
                            <a href="{{ route('goals.show', $goal) }}" class="block">
                                <div class="flex items-center gap-3 mb-1">
                                    <div class="w-2 h-2 rounded-full" style="background-color: {{ $goal->lifeArea->color }}"></div>
                                    <span class="text-sm font-medium text-gray-900 flex-1">{{ $goal->title }}</span>
                                    <span class="text-xs font-bold text-gray-700">{{ $goal->progress }}%</span>
                                </div>
                                <x-ui.progress-bar :value="$goal->progress" color="indigo" size="xs" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- Progression Sidebar --}}
        <div class="space-y-6">
            {{-- Level & XP --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">Level {{ Auth::user()->level ?? 1 }}</p>
                        <x-ui.rank-badge :rank="Auth::user()->rank ?? 'initiate'" />
                    </div>
                </div>
                @php
                    $user = Auth::user();
                    $xpProgress = $user->xpProgress();
                    $xpNeeded = $user->xpNeeded();
                @endphp
                <x-ui.progress-bar :value="$xpProgress" :max="max($xpNeeded, 1)" color="indigo" size="md" :showLabel="true">
                    XP to next level
                </x-ui.progress-bar>
                <p class="mt-2 text-xs text-gray-500">{{ $xpProgress }} / {{ $xpNeeded }} XP</p>
            </x-ui.card>

            {{-- Stats --}}
            <x-ui.card>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Stats</h3>
                <div class="space-y-3">
                    @foreach(\App\Models\User::STATS as $stat)
                        @php $statXp = $stats[$stat] ?? 0; @endphp
                        <x-ui.stat-bar :stat="$stat" :value="min($statXp, 100)" />
                    @endforeach
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
