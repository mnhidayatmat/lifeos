<x-app-layout>
    <x-slot name="header">{{ $goal->title }}</x-slot>
    <x-slot name="title">{{ $goal->title }}</x-slot>

    <div class="max-w-4xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Goal info --}}
                <x-ui.card>
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $goal->lifeArea->color }}"></div>
                                <span class="text-xs font-medium" style="color: {{ $goal->lifeArea->color }}">{{ $goal->lifeArea->name }}</span>
                            </div>
                            <h1 class="text-lg font-semibold text-gray-900">{{ $goal->title }}</h1>
                            @if($goal->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $goal->description }}</p>
                            @endif
                        </div>
                        <a href="{{ route('goals.edit', $goal) }}" class="text-sm text-gray-500 hover:text-gray-700">Edit</a>
                    </div>

                    {{-- Progress --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Progress</span>
                            <span class="text-sm font-bold text-gray-900">{{ $goal->progress }}%</span>
                        </div>
                        <x-ui.progress-bar :value="$goal->progress" color="indigo" size="lg" />
                    </div>

                    {{-- KPI / Manual progress update --}}
                    @if($goal->progress_type === 'kpi_based')
                        <form method="POST" action="{{ route('goals.update-progress', $goal) }}" class="flex items-center gap-2 mt-3 p-3 bg-gray-50 rounded-lg">
                            @csrf
                            @method('PATCH')
                            <span class="text-xs text-gray-500">Current:</span>
                            <input type="number" name="current_value" step="0.01" min="0"
                                   value="{{ $goal->current_value }}"
                                   class="w-24 rounded-md border-gray-300 text-sm">
                            <span class="text-xs text-gray-500">/ {{ $goal->target_value }}</span>
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">Update</button>
                        </form>
                    @elseif($goal->progress_type === 'manual')
                        <form method="POST" action="{{ route('goals.update-progress', $goal) }}" class="flex items-center gap-2 mt-3 p-3 bg-gray-50 rounded-lg">
                            @csrf
                            @method('PATCH')
                            <span class="text-xs text-gray-500">Progress:</span>
                            <input type="range" name="manual_progress" min="0" max="100" value="{{ $goal->manual_progress }}"
                                   class="flex-1" oninput="this.nextElementSibling.textContent = this.value + '%'">
                            <span class="text-xs font-medium text-gray-700 w-10">{{ $goal->manual_progress }}%</span>
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">Update</button>
                        </form>
                    @endif

                    {{-- Status / Meta --}}
                    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('goals.update-status', $goal) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                    class="rounded-md border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(['not_started', 'in_progress', 'on_hold', 'completed', 'abandoned'] as $status)
                                    <option value="{{ $status }}" @selected($goal->status === $status)>
                                        {{ str_replace('_', ' ', ucfirst($status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @if($goal->due_date)
                            <span class="text-xs text-gray-500">Due {{ $goal->due_date->format('M j, Y') }}</span>
                        @endif
                        <span class="text-xs text-gray-400">{{ ucfirst($goal->priority) }} priority</span>
                    </div>
                </x-ui.card>

                {{-- Projects --}}
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Projects</h2>
                    </div>
                    @if($goal->projects->isEmpty())
                        <p class="text-sm text-gray-400">No projects linked to this goal yet.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($goal->projects as $project)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $project->title }}</span>
                                        <span class="text-xs text-gray-400 ml-2">{{ $project->tasks->count() }} tasks</span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600">{{ $project->progress }}%</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>

                {{-- Standalone Tasks --}}
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-gray-900">Tasks (not in a project)</h2>
                    </div>
                    @if($goal->tasks->isEmpty())
                        <p class="text-sm text-gray-400">No standalone tasks for this goal.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($goal->tasks as $task)
                                <div class="flex items-center gap-3 p-2">
                                    <div class="w-5 h-5 rounded border-2 {{ $task->isCompleted() ? 'bg-indigo-500 border-indigo-500' : 'border-gray-300' }} flex items-center justify-center">
                                        @if($task->isCompleted())
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-sm {{ $task->isCompleted() ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $task->title }}</span>
                                    <x-ui.effort-badge :effort="$task->effort" />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <x-ui.card>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Stats Contribution</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Primary</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($goal->lifeArea->primary_stat) }} (70%)</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Secondary</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($goal->lifeArea->secondary_stat) }} (30%)</span>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Summary</h3>
                    <div class="space-y-2 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Total projects</span>
                            <span class="font-medium text-gray-700">{{ $goal->projects->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total tasks</span>
                            <span class="font-medium text-gray-700">{{ $goal->projects->sum(fn($p) => $p->tasks->count()) + $goal->tasks->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Created</span>
                            <span class="font-medium text-gray-700">{{ $goal->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
