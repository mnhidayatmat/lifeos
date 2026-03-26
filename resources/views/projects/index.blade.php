<x-app-layout>
    <x-slot name="header">Projects</x-slot>
    <x-slot name="title">Projects</x-slot>

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('projects.index') }}"
                   class="px-3 py-1.5 text-sm rounded-lg {{ !$areaFilter && !$goalFilter ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:bg-gray-100' }}">
                    All
                </a>
                @foreach($areas as $area)
                    <a href="{{ route('projects.index', ['area' => $area->id]) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $areaFilter == $area->id ? 'font-medium' : 'text-gray-500 hover:bg-gray-100' }}"
                       style="{{ $areaFilter == $area->id ? 'background-color: ' . $area->color . '15; color: ' . $area->color : '' }}">
                        {{ $area->name }}
                    </a>
                @endforeach
            </div>
            <button @click="$dispatch('open-modal-create-project')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shrink-0">
                <x-icon name="plus" class="w-4 h-4" />
                New Project
            </button>
        </div>

        @if($projects->isEmpty())
            <x-ui.card>
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                        <x-icon name="folder" class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No projects yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">Create a project to organise tasks under your goals.</p>
                    <button @click="$dispatch('open-modal-create-project')"
                            class="mt-4 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
                        <x-icon name="plus" class="w-4 h-4" />
                        Create Project
                    </button>
                </div>
            </x-ui.card>
        @else
            <div class="space-y-3">
                @foreach($projects as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block">
                        <x-ui.card class="hover:border-gray-300 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-2 h-10 rounded-full shrink-0" style="background-color: {{ $project->lifeArea->color }}"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $project->title }}</h3>
                                        @php
                                            $statusColors = ['not_started' => 'gray', 'in_progress' => 'blue', 'on_hold' => 'amber', 'completed' => 'emerald', 'archived' => 'gray'];
                                        @endphp
                                        <x-ui.badge :color="$statusColors[$project->status] ?? 'gray'" size="xs">
                                            {{ str_replace('_', ' ', ucfirst($project->status)) }}
                                        </x-ui.badge>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                                        <span style="color: {{ $project->lifeArea->color }}">{{ $project->lifeArea->name }}</span>
                                        @if($project->goal)
                                            <span>{{ $project->goal->title }}</span>
                                        @endif
                                        <span>{{ $project->tasks->count() }} tasks</span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 flex items-center gap-3">
                                    @if($project->ice_score !== null)
                                        <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950 px-1.5 py-0.5 rounded" title="ICE Score">{{ $project->ice_score }}</span>
                                    @endif
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->progress }}%</span>
                                </div>
                            </div>
                        </x-ui.card>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Create Modal --}}
    <x-ui.modal name="create-project" maxWidth="lg">
        <form method="POST" action="{{ route('projects.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Create Project</h2>
            @include('projects._form', ['project' => null])
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="$dispatch('close-modal-create-project')" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
