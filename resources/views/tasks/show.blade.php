<x-app-layout>
    <x-slot name="header">{{ $task->title }}</x-slot>
    <x-slot name="title">{{ $task->title }}</x-slot>

    <div class="max-w-2xl">
        <x-ui.card>
            {{-- Header --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ $task->isCompleted() ? route('tasks.reopen', $task) : route('tasks.complete', $task) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-6 h-6 rounded border-2 {{ $task->isCompleted() ? 'bg-indigo-500 border-indigo-500' : 'border-gray-300 hover:border-indigo-400' }} flex items-center justify-center transition-colors">
                            @if($task->isCompleted())
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </button>
                    </form>
                    <div>
                        <h1 class="text-lg font-semibold {{ $task->isCompleted() ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $task->title }}</h1>
                        @if($task->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $task->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.effort-badge :effort="$task->effort" />
                    <span class="text-xs text-gray-500">{{ $task->base_xp }} XP</span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="flex items-center gap-4 text-xs text-gray-500 mb-4 pb-4 border-b border-gray-100">
                @if($task->project)
                    <a href="{{ route('projects.show', $task->project) }}" class="hover:text-indigo-600">{{ $task->project->title }}</a>
                @endif
                @if($task->goal)
                    <a href="{{ route('goals.show', $task->goal) }}" class="hover:text-indigo-600">{{ $task->goal->title }}</a>
                @endif
                @if($task->due_date)
                    <span class="{{ $task->isOverdue() ? 'text-rose-500' : '' }}">Due {{ $task->due_date->format('M j, Y') }}</span>
                @endif
                <span>{{ ucfirst($task->priority) }} priority</span>
                @if($task->is_important)
                    <span class="text-amber-500 dark:text-amber-400 font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        Important
                    </span>
                @endif
                @if($task->is_recurring)
                    <span class="text-indigo-500">Recurring: {{ $task->recurrence_rule }}</span>
                @endif
            </div>

            {{-- Subtasks --}}
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Subtasks</h3>
                @if($task->subtasks->isNotEmpty())
                    <div class="space-y-1 mb-3">
                        @foreach($task->subtasks as $subtask)
                            <div class="flex items-center gap-3 p-1.5 rounded hover:bg-gray-50 group">
                                <form method="POST" action="{{ route('subtasks.toggle', $subtask) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-4 h-4 rounded border {{ $subtask->is_completed ? 'bg-indigo-500 border-indigo-500' : 'border-gray-300' }} flex items-center justify-center">
                                        @if($subtask->is_completed)
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                <span class="text-sm {{ $subtask->is_completed ? 'text-gray-400 line-through' : 'text-gray-700' }} flex-1">{{ $subtask->title }}</span>
                                <form method="POST" action="{{ route('subtasks.destroy', $subtask) }}" class="hidden group-hover:block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-300 hover:text-rose-500">
                                        <x-icon name="x-mark" class="w-3 h-3" />
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('subtasks.store', $task) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="title" placeholder="Add subtask..."
                           class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-3 py-2 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">Add</button>
                </form>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700">Delete task</button>
                </form>
                @if($task->isCompleted())
                    <span class="text-xs text-gray-400">Completed {{ $task->completed_at->format('M j, Y g:ia') }}</span>
                @endif
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
