<x-app-layout>
    <x-slot name="header">Tasks</x-slot>
    <x-slot name="title">Tasks</x-slot>

    <div class="max-w-4xl">
        {{-- Filter bar — sticky under the topbar on mobile for quick switching --}}
        <div class="sticky top-16 z-10 -mx-4 px-4 py-2 bg-gray-50/90 dark:bg-gray-950/90 backdrop-blur
                    sm:static sm:mx-0 sm:px-0 sm:py-0 sm:bg-transparent sm:backdrop-blur-none
                    mb-3 sm:mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-1 overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0 no-scrollbar">
                @php
                    $overdueCount = auth()->user()->tasks()->where('due_date', '<', today())->whereNotIn('status', ['completed','cancelled'])->count();
                @endphp
                @foreach(['today' => 'Today', 'overdue' => 'Overdue', 'all' => 'All', 'matrix' => 'Matrix'] as $key => $label)
                    <a href="{{ route('tasks.index', ['view' => $key]) }}"
                       class="shrink-0 whitespace-nowrap px-3 py-1.5 text-sm rounded-lg transition-colors {{ $view === $key ? 'bg-teal-600 text-white font-medium shadow-sm' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                        {{ $label }}
                        @if($key === 'overdue' && $overdueCount > 0)
                            <span class="ml-1 px-1.5 py-0.5 text-[10px] rounded-full {{ $view === $key ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-400' }}">{{ $overdueCount }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            {{-- Desktop create button (mobile uses the floating action button below) --}}
            <button @click="$dispatch('open-modal-create-task')"
                    class="hidden sm:inline-flex shrink-0 items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                New Task
            </button>
        </div>

        {{-- Count --}}
        @if($tasks->isNotEmpty())
            <p class="px-1 mb-2 text-xs text-gray-400 dark:text-gray-500">{{ $tasks->count() }} {{ \Illuminate\Support\Str::plural('task', $tasks->count()) }}</p>
        @endif

        {{-- Task list --}}
        @if($tasks->isEmpty())
            <x-ui.card>
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                        <x-icon name="check-square" class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                        {{ $view === 'overdue' ? 'No overdue tasks' : ($view === 'today' ? 'Nothing due today' : 'No tasks yet') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                        {{ $view === 'today' ? "You're all caught up. Add a task for today." : 'Create your first task to start tracking progress.' }}
                    </p>
                    <button @click="$dispatch('open-modal-create-task')"
                            class="mt-4 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900 transition-colors">
                        <x-icon name="plus" class="w-4 h-4" />
                        Add Task
                    </button>
                </div>
            </x-ui.card>
        @else
            <div class="space-y-2">
                @foreach($tasks as $task)
                    <div class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 px-3.5 py-3 flex items-start gap-3 transition-colors hover:border-gray-300 dark:hover:border-gray-700">
                        {{-- Checkbox --}}
                        <form method="POST" action="{{ $task->isCompleted() ? route('tasks.reopen', $task) : route('tasks.complete', $task) }}" class="shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" aria-label="{{ $task->isCompleted() ? 'Reopen task' : 'Complete task' }}"
                                    class="mt-0.5 w-[22px] h-[22px] rounded-full border-2 {{ $task->isCompleted() ? 'bg-teal-500 border-teal-500' : 'border-gray-300 dark:border-gray-600 hover:border-teal-400 active:scale-90' }} flex items-center justify-center transition-all">
                                @if($task->isCompleted())
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                        </form>

                        {{-- Task info --}}
                        <a href="{{ route('tasks.show', $task) }}" class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-medium truncate {{ $task->isCompleted() ? 'text-gray-400 dark:text-gray-600 line-through' : 'text-gray-900 dark:text-gray-100' }}">{{ $task->title }}</span>
                                @if($task->is_important)
                                    <svg class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24" title="Important">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                @endif
                                @if($task->is_recurring)
                                    <svg class="w-3.5 h-3.5 text-teal-400 dark:text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Recurring">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Meta row --}}
                            @if($task->project || $task->goal || $task->due_date)
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    @if($task->project)
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 max-w-[10rem] truncate">
                                            <x-icon name="folder" class="w-3 h-3 shrink-0" />
                                            <span class="truncate">{{ $task->project->title }}</span>
                                        </span>
                                    @elseif($task->goal)
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 max-w-[10rem] truncate">
                                            <x-icon name="target" class="w-3 h-3 shrink-0" />
                                            <span class="truncate">{{ $task->goal->title }}</span>
                                        </span>
                                    @endif
                                    @if($task->due_date)
                                        <span class="inline-flex items-center gap-1 text-xs {{ $task->isOverdue() ? 'text-rose-500 dark:text-rose-400 font-medium' : 'text-gray-400 dark:text-gray-500' }}">
                                            <x-icon name="clock" class="w-3 h-3 shrink-0" />
                                            {{ $task->due_date->isToday() ? 'Today' : $task->due_date->format('M j') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </a>

                        {{-- Right side --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <x-ui.effort-badge :effort="$task->effort" />
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')"
                                  class="opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 -mr-1 text-gray-300 dark:text-gray-600 hover:text-rose-500 dark:hover:text-rose-400 transition-colors" title="Delete task">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Floating action button — mobile-only, thumb-zone task creation --}}
    <button @click="$dispatch('open-modal-create-task')" aria-label="New task"
            class="sm:hidden fixed right-4 z-40 w-14 h-14 rounded-full bg-teal-600 text-white shadow-lg shadow-teal-600/30 flex items-center justify-center active:bg-teal-700 active:scale-95 transition"
            style="bottom: calc(env(safe-area-inset-bottom) + 5rem);">
        <x-icon name="plus" class="w-6 h-6" />
    </button>

    {{-- Create Task Modal --}}
    <x-ui.modal name="create-task" maxWidth="lg">
        <form method="POST" action="{{ route('tasks.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create Task</h2>
            @include('tasks._form', ['task' => null])
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="$dispatch('close-modal-create-task')" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700">Create</button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
