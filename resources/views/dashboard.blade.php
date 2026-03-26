<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="title">Dashboard</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Column --}}
        <div class="lg:col-span-2 space-y-6">
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

            {{-- Active Goals --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Active Goals</h2>
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
