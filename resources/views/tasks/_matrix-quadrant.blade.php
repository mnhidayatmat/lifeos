@if($tasks->isEmpty())
    <p class="text-xs text-gray-400 dark:text-gray-500 italic py-3">{{ $emptyText }}</p>
@else
    <div class="space-y-1">
        @foreach($tasks as $task)
            <div class="flex items-center gap-2.5 p-2 rounded-lg bg-white/70 dark:bg-gray-900/50 hover:bg-white dark:hover:bg-gray-900 transition-colors group">
                {{-- Complete checkbox --}}
                <form method="POST" action="{{ route('tasks.complete', $task) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600 hover:border-indigo-400 flex items-center justify-center transition-colors shrink-0"></button>
                </form>

                {{-- Task info --}}
                <a href="{{ route('tasks.show', $task) }}" class="flex-1 min-w-0">
                    <span class="text-sm text-gray-800 dark:text-gray-200 truncate block">{{ $task->title }}</span>
                </a>

                {{-- Effort + date --}}
                <x-ui.effort-badge :effort="$task->effort" />
                @if($task->due_date)
                    <span class="text-[10px] {{ $task->isOverdue() ? 'text-rose-500' : 'text-gray-400 dark:text-gray-500' }} shrink-0">
                        {{ $task->due_date->isToday() ? 'Today' : $task->due_date->format('M j') }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>
@endif
