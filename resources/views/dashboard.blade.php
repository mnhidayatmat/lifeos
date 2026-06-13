<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="title">Home</x-slot>
    {{-- On mobile the gradient hero below owns the top of the screen, so suppress the white topbar there. --}}
    <x-slot name="hideMobileTopbar">true</x-slot>

    @php
        $firstName  = explode(' ', trim(Auth::user()->name))[0] ?: 'there';
        $initial    = strtoupper(substr(Auth::user()->name, 0, 1));
        $hour       = now()->hour;
        $greeting   = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $unread     = Auth::user()->unreadNotifications()->count();
        $overdueCount = $overdueTasks->count();
        $dueTodayCount = $todayTasks->count();

        $chips = [
            ['label' => 'Tasks',    'icon' => 'check-square', 'route' => route('tasks.index')],
            ['label' => 'Goals',    'icon' => 'target',       'route' => route('goals.index')],
            ['label' => 'Projects', 'icon' => 'folder',       'route' => route('projects.index')],
            ['label' => 'Habits',   'icon' => 'repeat',       'route' => route('habits.index')],
            ['label' => 'Review',   'icon' => 'book-open',    'route' => route('reviews.daily')],
        ];

        $features = [
            ['label' => 'Life Areas', 'icon' => 'grid',      'route' => route('life-areas.index'), 'tint' => 'text-teal-600 bg-teal-50 dark:bg-teal-950/60 dark:text-teal-400'],
            ['label' => 'Vision',     'icon' => 'eye',       'route' => route('vision.index'),     'tint' => 'text-violet-600 bg-violet-50 dark:bg-violet-950/60 dark:text-violet-400'],
            ['label' => 'Analytics',  'icon' => 'chart',     'route' => route('analytics.index'),  'tint' => 'text-blue-600 bg-blue-50 dark:bg-blue-950/60 dark:text-blue-400'],
            ['label' => 'Milestones', 'icon' => 'trophy',    'route' => route('milestones.index'), 'tint' => 'text-amber-600 bg-amber-50 dark:bg-amber-950/60 dark:text-amber-400'],
            ['label' => 'Library',    'icon' => 'book-open', 'route' => route('resources.index'),  'tint' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-400'],
        ];
    @endphp

    {{-- ============================================================= --}}
    {{-- MOBILE — app-style home (wireframe). Hidden on lg+.            --}}
    {{-- ============================================================= --}}
    <div class="lg:hidden -mx-4 -mt-4 sm:-mx-6 sm:-mt-6">

        {{-- Hero: greeting, actions, quick-add, quick chips --}}
        <section class="relative bg-gradient-to-br from-teal-600 via-teal-500 to-violet-500 text-white px-5 pb-12"
                 style="padding-top: calc(env(safe-area-inset-top) + 1.25rem);">

            {{-- Top row --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-white/70">{{ $greeting }},</p>
                    <h1 class="text-xl font-bold leading-tight">{{ $firstName }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('notifications.index') }}" aria-label="Notifications"
                       class="relative w-9 h-9 rounded-full bg-white/15 backdrop-blur flex items-center justify-center active:bg-white/25 transition">
                        <x-icon name="bell" class="w-5 h-5 text-white" />
                        @if($unread > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-teal-700 bg-white rounded-full flex items-center justify-center">{{ min($unread, 9) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile.edit') }}" aria-label="Profile"
                       class="w-9 h-9 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-sm font-semibold text-white active:bg-white/30 transition">
                        {{ $initial }}
                    </a>
                </div>
            </div>

            {{-- Quick add (the "search" pill) --}}
            <form method="POST" action="{{ route('tasks.store') }}" class="mt-5">
                @csrf
                <input type="hidden" name="due_date" value="{{ today()->format('Y-m-d') }}">
                <input type="hidden" name="effort" value="medium">
                <label class="flex items-center gap-2.5 bg-white rounded-2xl px-4 h-12 shadow-sm shadow-teal-900/10">
                    <x-icon name="plus" class="w-5 h-5 text-teal-500 shrink-0" />
                    <input type="text" name="title" placeholder="Quick add a task for today…" required
                           class="flex-1 min-w-0 border-0 bg-transparent text-sm text-gray-900 placeholder-gray-400 focus:ring-0 p-0">
                </label>
            </form>

            {{-- Quick action chips --}}
            <div class="grid grid-cols-5 gap-1 mt-5">
                @foreach($chips as $chip)
                    <a href="{{ $chip['route'] }}" class="flex flex-col items-center gap-1.5 group">
                        <span class="w-12 h-12 rounded-full bg-white/15 backdrop-blur flex items-center justify-center group-active:bg-white/25 transition">
                            <x-icon :name="$chip['icon']" class="w-5 h-5 text-white" />
                        </span>
                        <span class="text-[11px] font-medium text-white/90">{{ $chip['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Content sheet — overlaps the hero for a layered, app-like feel --}}
        <div class="relative -mt-6 rounded-t-[1.75rem] bg-gray-50 dark:bg-gray-950 px-4 pt-5 pb-2 space-y-4">

            {{-- Highlight banner: #1 goal, else daily-review nudge --}}
            @if($dominoGoal)
                <div x-data="{ show: true }" x-show="show"
                     class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4">
                    <div class="flex items-start gap-3">
                        <span class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center shrink-0">
                            <x-icon name="target" class="w-5 h-5 text-amber-500" />
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Your #1 focus</p>
                            <a href="{{ route('goals.show', $dominoGoal) }}" class="block text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $dominoGoal->title }}</a>
                        </div>
                        <button @click="show = false" class="text-gray-300 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400 -mt-1 -mr-1 p-1" aria-label="Dismiss">
                            <x-icon name="x-mark" class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <x-ui.progress-bar :value="$dominoGoal->progress" color="amber" size="xs" class="flex-1" />
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 tabular-nums">{{ $dominoGoal->progress }}%</span>
                    </div>
                </div>
            @else
                <a href="{{ route('reviews.daily') }}"
                   class="block rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 active:bg-gray-50 dark:active:bg-gray-800/50 transition">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/60 flex items-center justify-center shrink-0">
                            <x-icon name="book-open" class="w-5 h-5 text-teal-500" />
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Reflect on your day</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Run a quick daily review to stay on track.</p>
                        </div>
                        <x-icon name="arrow-right" class="w-4 h-4 text-gray-300 dark:text-gray-600 shrink-0" />
                    </div>
                </a>
            @endif

            {{-- Two stat cards --}}
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('tasks.index') }}" class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 active:bg-gray-50 dark:active:bg-gray-800/50 transition">
                    <span class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 flex items-center justify-center">
                        <x-icon name="check-square" class="w-5 h-5 text-teal-500" />
                    </span>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums mt-3">{{ $dueTodayCount }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $dueTodayCount === 1 ? 'task' : 'tasks' }} due today</p>
                </a>
                <a href="{{ route('analytics.index') }}" class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 active:bg-gray-50 dark:active:bg-gray-800/50 transition">
                    <span class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 flex items-center justify-center">
                        <x-icon name="fire" class="w-5 h-5 text-rose-500" />
                    </span>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums mt-3">{{ $currentStreak }}<span class="text-sm font-medium text-gray-400"> day{{ $currentStreak === 1 ? '' : 's' }}</span></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">current streak</p>
                </a>
            </div>

            {{-- Task pipeline (the "orders" grid) --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="grid grid-cols-4 divide-x divide-gray-100 dark:divide-gray-800">
                    @php
                        $pipeline = [
                            ['label' => 'To do',    'count' => $taskTodoCount,       'icon' => 'check-square', 'tint' => 'text-gray-500 bg-gray-100 dark:bg-gray-800'],
                            ['label' => 'Doing',    'count' => $taskInProgressCount, 'icon' => 'clock',        'tint' => 'text-blue-500 bg-blue-50 dark:bg-blue-950/60'],
                            ['label' => 'Done',     'count' => $tasksToday,          'icon' => 'check-square', 'tint' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-950/60'],
                            ['label' => 'Overdue',  'count' => $overdueCount,        'icon' => 'clock',        'tint' => 'text-rose-500 bg-rose-50 dark:bg-rose-950/60'],
                        ];
                    @endphp
                    @foreach($pipeline as $col)
                        <a href="{{ route('tasks.index') }}" class="flex flex-col items-center gap-1.5 py-3.5 active:bg-gray-50 dark:active:bg-gray-800/50 transition">
                            <span class="w-9 h-9 rounded-full {{ $col['tint'] }} flex items-center justify-center">
                                <x-icon :name="$col['icon']" class="w-5 h-5" />
                            </span>
                            <span class="text-base font-bold text-gray-900 dark:text-white tabular-nums leading-none">{{ $col['count'] }}</span>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 leading-none">{{ $col['label'] }}</span>
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('tasks.index') }}" class="flex items-center justify-center gap-1.5 py-3 border-t border-gray-100 dark:border-gray-800 text-sm font-medium text-teal-600 dark:text-teal-400 active:bg-gray-50 dark:active:bg-gray-800/50 transition">
                    View all tasks
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </a>
            </div>

            {{-- Today's tasks --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Today</h2>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ now()->format('D, M j') }}</span>
                </div>
                @if($todayTasks->isEmpty())
                    <div class="flex flex-col items-center text-center py-6">
                        <span class="w-11 h-11 rounded-full bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center mb-2">
                            <x-icon name="check-square" class="w-5 h-5 text-emerald-500" />
                        </span>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">All clear for today</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Add a task above to get going.</p>
                    </div>
                @else
                    <div class="space-y-0.5">
                        @foreach($todayTasks as $task)
                            <div class="flex items-center gap-3 -mx-1.5 px-1.5 py-2 rounded-lg active:bg-gray-50 dark:active:bg-gray-800/50">
                                <form method="POST" action="{{ route('tasks.complete', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" aria-label="Complete task" class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-600 hover:border-teal-400 transition-colors"></button>
                                </form>
                                <a href="{{ route('tasks.show', $task) }}" class="flex-1 min-w-0">
                                    <span class="text-sm text-gray-900 dark:text-gray-100 truncate block">{{ $task->title }}</span>
                                </a>
                                <x-ui.effort-badge :effort="$task->effort" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Today's habits --}}
            @if($todayHabits->isNotEmpty())
                <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Habits</h2>
                        <a href="{{ route('habits.index') }}" class="text-xs text-teal-600 dark:text-teal-400">View all</a>
                    </div>
                    <div class="space-y-0.5">
                        @foreach($todayHabits as $habit)
                            <div class="flex items-center gap-3 -mx-1.5 px-1.5 py-2 rounded-lg active:bg-gray-50 dark:active:bg-gray-800/50">
                                <form method="POST" action="{{ route('habits.toggle', $habit) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" aria-label="Toggle habit" class="w-5 h-5 rounded-full border-2 {{ $habit->isCompletedToday() ? 'bg-emerald-500 border-emerald-500' : 'border-gray-300 dark:border-gray-600 hover:border-emerald-400' }} flex items-center justify-center transition-colors">
                                        @if($habit->isCompletedToday())
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <span class="text-sm flex-1 truncate {{ $habit->isCompletedToday() ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-gray-100' }}">{{ $habit->title }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 capitalize">{{ $habit->routine }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Active goals --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Active goals</h2>
                    <a href="{{ route('goals.index') }}" class="text-xs text-teal-600 dark:text-teal-400">View all</a>
                </div>
                @if($activeGoals->isEmpty())
                    <a href="{{ route('goals.create') }}" class="flex items-center justify-center gap-1.5 py-4 text-sm font-medium text-teal-600 dark:text-teal-400">
                        <x-icon name="plus" class="w-4 h-4" /> Create your first goal
                    </a>
                @else
                    <div class="space-y-3.5">
                        @foreach($activeGoals as $goal)
                            <a href="{{ route('goals.show', $goal) }}" class="block">
                                <div class="flex items-center gap-2.5 mb-1.5">
                                    <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $goal->lifeArea->color }}"></span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 flex-1 truncate">{{ $goal->title }}</span>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300 tabular-nums">{{ $goal->progress }}%</span>
                                </div>
                                <x-ui.progress-bar :value="$goal->progress" color="teal" size="xs" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Explore shortcuts (horizontal scroll) --}}
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 px-1">Explore</h2>
                <div class="flex gap-3 overflow-x-auto pb-1 -mx-4 px-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    @foreach($features as $feature)
                        <a href="{{ $feature['route'] }}" class="shrink-0 w-[72px] flex flex-col items-center gap-1.5">
                            <span class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $feature['tint'] }}">
                                <x-icon :name="$feature['icon']" class="w-6 h-6" />
                            </span>
                            <span class="text-[11px] font-medium text-gray-600 dark:text-gray-300 text-center leading-tight">{{ $feature['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Weekly momentum (the "promo" card) --}}
            <a href="{{ route('reviews.weekly') }}"
               class="block rounded-2xl bg-gradient-to-br from-teal-600 to-violet-500 text-white p-5 active:opacity-95 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-white/70">This week</p>
                        <p class="text-3xl font-bold tabular-nums mt-0.5">{{ $tasksThisWeek }}</p>
                        <p class="text-sm text-white/80">{{ $tasksThisWeek === 1 ? 'task' : 'tasks' }} completed</p>
                    </div>
                    <span class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center shrink-0">
                        <x-icon name="chart" class="w-6 h-6 text-white" />
                    </span>
                </div>
                <div class="flex items-center gap-1.5 mt-4 text-sm font-medium">
                    Start your weekly review
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </div>
            </a>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- DESKTOP — original dashboard layout. Hidden below lg.         --}}
    {{-- ============================================================= --}}
    <div class="hidden lg:grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        {{-- Main Column --}}
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            {{-- Domino Goal --}}
            @if($dominoGoal)
                <x-ui.card class="border-amber-200 dark:border-amber-900/50 bg-amber-50/30 dark:bg-amber-950/10">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wider">Your #1 Domino Goal</p>
                            <a href="{{ route('goals.show', $dominoGoal) }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-teal-600">{{ $dominoGoal->title }}</a>
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
                                    <button type="submit" class="w-5 h-5 rounded border-2 border-gray-300 hover:border-teal-400 flex items-center justify-center transition-colors"></button>
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
                           class="flex-1 rounded-lg border-gray-200 bg-gray-50 text-sm focus:border-teal-500 focus:ring-teal-500 focus:bg-white" required>
                    <button type="submit" class="px-3 py-2 text-xs font-medium text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100">Add</button>
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
                        <a href="{{ route('habits.index') }}" class="text-xs text-teal-600 hover:text-teal-700 dark:text-teal-400">View all</a>
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
                    <a href="{{ route('goals.index') }}" class="text-xs text-teal-600 hover:text-teal-700">View all</a>
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
                                <x-ui.progress-bar :value="$goal->progress" color="teal" size="xs" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- Insights Sidebar --}}
        <div class="space-y-4 lg:space-y-6">
            {{-- This Week --}}
            <x-ui.card>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 lg:mb-4">This week</h3>
                <div class="grid grid-cols-4 lg:grid-cols-2 gap-2 lg:gap-3">
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-2.5 lg:p-3">
                        <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $tasksThisWeek }}</p>
                        <p class="text-[11px] lg:text-xs leading-tight text-gray-500 dark:text-gray-400 mt-0.5">Tasks done</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-2.5 lg:p-3">
                        <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $tasksToday }}</p>
                        <p class="text-[11px] lg:text-xs leading-tight text-gray-500 dark:text-gray-400 mt-0.5">Done today</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-2.5 lg:p-3">
                        <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $currentStreak }}<span class="hidden lg:inline text-sm font-normal text-gray-400"> day{{ $currentStreak === 1 ? '' : 's' }}</span></p>
                        <p class="text-[11px] lg:text-xs leading-tight text-gray-500 dark:text-gray-400 mt-0.5">Day streak</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-2.5 lg:p-3">
                        <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $activeGoalCount }}</p>
                        <p class="text-[11px] lg:text-xs leading-tight text-gray-500 dark:text-gray-400 mt-0.5">Active goals</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Life Area Progress --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Life areas</h3>
                    <a href="{{ route('analytics.index') }}" class="text-xs text-teal-600 hover:text-teal-700 dark:text-teal-400">Analytics</a>
                </div>
                @if($lifeAreaProgress->isEmpty())
                    <p class="text-sm text-gray-400">No active life areas.</p>
                @else
                    <div class="space-y-3">
                        @foreach($lifeAreaProgress as $area)
                            <a href="{{ route('life-areas.index') }}" class="flex items-center gap-3 group">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $area->color }}"></span>
                                <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate group-hover:text-gray-900 dark:group-hover:text-white">{{ $area->name }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $area->active_goals_count }} goal{{ $area->active_goals_count === 1 ? '' : 's' }}</span>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300 tabular-nums w-10 text-right">{{ $area->tasks_this_week }} <span class="text-gray-400">done</span></span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
