<x-app-layout>
    <x-slot name="header">Tasks</x-slot>
    <x-slot name="title">Tasks</x-slot>

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-1">
                @foreach(['today' => 'Today', 'overdue' => 'Overdue', 'all' => 'All', 'matrix' => 'Matrix'] as $key => $label)
                    <a href="{{ route('tasks.index', ['view' => $key]) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $view === $key ? 'bg-indigo-50 text-indigo-700 font-medium dark:bg-indigo-950 dark:text-indigo-400' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                        {{ $label }}
                        @if($key === 'overdue')
                            @php $overdueCount = auth()->user()->tasks()->where('due_date', '<', today())->whereNotIn('status', ['completed','cancelled'])->count(); @endphp
                            @if($overdueCount > 0)
                                <span class="ml-1 px-1.5 py-0.5 text-[10px] bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-400 rounded-full">{{ $overdueCount }}</span>
                            @endif
                        @endif
                    </a>
                @endforeach
            </div>
            <button @click="$dispatch('open-modal-create-task')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                New Task
            </button>
        </div>

        {{-- Task list --}}
        @if($tasks->isEmpty())
            <x-ui.card>
                <x-ui.empty-state
                    icon="check-square"
                    :title="$view === 'overdue' ? 'No overdue tasks' : ($view === 'today' ? 'Nothing due today' : 'No tasks yet')"
                    :description="$view === 'today' ? 'You\'re all caught up. Add a task for today.' : 'Create your first task to start tracking progress.'"
                    action="Add Task"
                />
            </x-ui.card>
        @else
            <div class="space-y-1">
                @foreach($tasks as $task)
                    <x-ui.card :padding="false" class="px-4 py-3 group">
                        <div class="flex items-center gap-3">
                            {{-- Checkbox --}}
                            <form method="POST" action="{{ $task->isCompleted() ? route('tasks.reopen', $task) : route('tasks.complete', $task) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-5 h-5 rounded border-2 {{ $task->isCompleted() ? 'bg-indigo-500 border-indigo-500' : 'border-gray-300 hover:border-indigo-400' }} flex items-center justify-center transition-colors">
                                    @if($task->isCompleted())
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>

                            {{-- Task info --}}
                            <a href="{{ route('tasks.show', $task) }}" class="flex-1 min-w-0">
                                <span class="text-sm {{ $task->isCompleted() ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $task->title }}</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($task->project)
                                        <span class="text-xs text-gray-400">{{ $task->project->title }}</span>
                                    @elseif($task->goal)
                                        <span class="text-xs text-gray-400">{{ $task->goal->title }}</span>
                                    @endif
                                </div>
                            </a>

                            {{-- Important flag --}}
                            @if($task->is_important)
                                <span class="text-xs text-amber-500 dark:text-amber-400" title="Important">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </span>
                            @endif

                            {{-- Effort --}}
                            <x-ui.effort-badge :effort="$task->effort" />

                            {{-- Due date --}}
                            @if($task->due_date)
                                <span class="text-xs {{ $task->isOverdue() ? 'text-rose-500 font-medium' : 'text-gray-400' }}">
                                    {{ $task->due_date->isToday() ? 'Today' : $task->due_date->format('M j') }}
                                </span>
                            @endif

                            {{-- Recurring badge --}}
                            @if($task->is_recurring)
                                <span class="text-xs text-indigo-400" title="Recurring">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                                    </svg>
                                </span>
                            @endif

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-gray-300 hover:text-rose-500 transition-colors" title="Delete task">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Create Task Modal --}}
    <x-ui.modal name="create-task" maxWidth="lg">
        <form method="POST" action="{{ route('tasks.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Create Task</h2>
            @include('tasks._form', ['task' => null])
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="$dispatch('close-modal-create-task')" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
