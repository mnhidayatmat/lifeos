<x-app-layout>
    <x-slot name="header">Knowledge Library</x-slot>
    <x-slot name="title">Knowledge Library</x-slot>

    <div class="max-w-4xl">
        {{-- Header with filter tabs and add button --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-1">
                @foreach(['all' => 'All', 'to_consume' => 'To Read', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $key => $label)
                    <a href="{{ route('resources.index', $key === 'all' ? [] : ['status' => $key]) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $statusFilter === $key ? 'bg-indigo-50 text-indigo-700 font-medium dark:bg-indigo-950 dark:text-indigo-400' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <button @click="$dispatch('open-modal-create-resource')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                Add Resource
            </button>
        </div>

        {{-- Resource list --}}
        @if($resources->isEmpty())
            <x-ui.card>
                <x-ui.empty-state
                    icon="book-open"
                    :title="$statusFilter !== 'all' ? 'No resources found' : 'No resources yet'"
                    :description="$statusFilter !== 'all' ? 'No resources match this filter. Try a different tab.' : 'Start building your knowledge library by adding a book, article, podcast, or course.'"
                    action="Add Resource"
                />
            </x-ui.card>
        @else
            <div class="space-y-2">
                @foreach($resources as $resource)
                    @php
                        $typeColors = [
                            'book'    => 'blue',
                            'article' => 'emerald',
                            'podcast' => 'purple',
                            'course'  => 'amber',
                            'video'   => 'rose',
                        ];
                        $statusColors = [
                            'to_consume'  => 'gray',
                            'in_progress' => 'indigo',
                            'completed'   => 'emerald',
                        ];
                        $statusLabels = [
                            'to_consume'  => 'To Read',
                            'in_progress' => 'In Progress',
                            'completed'   => 'Completed',
                        ];
                    @endphp

                    <x-ui.card>
                        <div class="flex items-start justify-between gap-4">
                            {{-- Left: resource info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-ui.badge :color="$typeColors[$resource->type] ?? 'gray'" size="xs">
                                        {{ ucfirst($resource->type) }}
                                    </x-ui.badge>
                                    <x-ui.badge :color="$statusColors[$resource->status] ?? 'gray'" size="xs">
                                        {{ $statusLabels[$resource->status] ?? ucfirst($resource->status) }}
                                    </x-ui.badge>
                                    @if($resource->lifeArea)
                                        <x-ui.badge color="teal" size="xs">
                                            {{ $resource->lifeArea->name }}
                                        </x-ui.badge>
                                    @endif
                                </div>

                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    @if($resource->url)
                                        <a href="{{ $resource->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                            {{ $resource->title }}
                                        </a>
                                    @else
                                        {{ $resource->title }}
                                    @endif
                                </h3>

                                @if($resource->author)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">by {{ $resource->author }}</p>
                                @endif
                            </div>

                            {{-- Right: rating and XP --}}
                            @if($resource->status === 'completed')
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    @if($resource->rating)
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= $resource->rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    @endif
                                    @if($resource->xp_awarded)
                                        <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">+{{ $resource->xp_awarded }} XP</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </x-ui.card>
                @endforeach
            </div>

            @if($resources->hasPages())
                <div class="mt-6">{{ $resources->links() }}</div>
            @endif
        @endif
    </div>

    {{-- Create Resource Modal --}}
    <x-ui.modal name="create-resource" maxWidth="lg">
        <form method="POST" action="{{ route('resources.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Resource</h2>

            {{-- Title --}}
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="title" required
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="e.g. Atomic Habits">
                @error('title')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select name="type" id="type"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="book">Book</option>
                    <option value="article">Article</option>
                    <option value="podcast">Podcast</option>
                    <option value="course">Course</option>
                    <option value="video">Video</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Author --}}
            <div class="mb-4">
                <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Author</label>
                <input type="text" name="author" id="author"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="e.g. James Clear">
                @error('author')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- URL --}}
            <div class="mb-4">
                <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL</label>
                <input type="url" name="url" id="url"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="https://...">
                @error('url')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Life Area --}}
            <div class="mb-4">
                <label for="life_area_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Life Area</label>
                <select name="life_area_id" id="life_area_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">None</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
                @error('life_area_id')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="to_consume">To Read</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="$dispatch('close-modal-create-resource')" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Add Resource</button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
