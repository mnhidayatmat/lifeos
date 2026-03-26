<x-app-layout>
    <x-slot name="header">{{ $project->title }}</x-slot>
    <x-slot name="title">{{ $project->title }}</x-slot>

    <div class="max-w-4xl">
        {{-- Project info --}}
        <x-ui.card class="mb-6">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $project->lifeArea->color }}"></div>
                        <span class="text-xs font-medium" style="color: {{ $project->lifeArea->color }}">{{ $project->lifeArea->name }}</span>
                        @if($project->goal)
                            <span class="text-xs text-gray-400">/</span>
                            <a href="{{ route('goals.show', $project->goal) }}" class="text-xs text-gray-500 hover:text-indigo-600">{{ $project->goal->title }}</a>
                        @endif
                    </div>
                    @if($project->description)
                        <p class="text-sm text-gray-500">{{ $project->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('projects.update-status', $project) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 text-xs">
                            @foreach(['not_started', 'in_progress', 'on_hold', 'completed', 'archived'] as $s)
                                <option value="{{ $s }}" @selected($project->status === $s)>{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <x-ui.progress-bar :value="$project->progress" color="indigo" size="sm" :showLabel="true">
                {{ $project->tasks->where('status', 'completed')->count() }} / {{ $project->tasks->count() }} tasks
            </x-ui.progress-bar>
        </x-ui.card>

        {{-- Tasks --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Tasks</h2>
            </div>

            {{-- Task list --}}
            @if($project->tasks->isEmpty())
                <p class="text-sm text-gray-400 mb-4">No tasks yet. Add your first task below.</p>
            @else
                <div class="space-y-1 mb-4">
                    @foreach($project->tasks->sortBy(fn($t) => $t->isCompleted() ? 1 : 0) as $task)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 group">
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
                            <a href="{{ route('tasks.show', $task) }}" class="flex-1 min-w-0">
                                <span class="text-sm {{ $task->isCompleted() ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $task->title }}</span>
                            </a>
                            <x-ui.effort-badge :effort="$task->effort" />
                            @if($task->due_date)
                                <span class="text-xs {{ $task->isOverdue() ? 'text-rose-500' : 'text-gray-400' }}">{{ $task->due_date->format('M j') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Inline add task --}}
            <form method="POST" action="{{ route('tasks.store') }}" class="flex items-center gap-2 pt-3 border-t border-gray-100">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="goal_id" value="{{ $project->goal_id }}">
                <input type="text" name="title" placeholder="Add a task..."
                       class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <select name="effort" class="rounded-lg border-gray-300 text-xs w-24">
                    <option value="small">Small</option>
                    <option value="medium" selected>Medium</option>
                    <option value="large">Large</option>
                </select>
                <input type="date" name="due_date" class="rounded-lg border-gray-300 text-xs">
                <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Add</button>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
