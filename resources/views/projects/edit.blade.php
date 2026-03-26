<x-app-layout>
    <x-slot name="header">Edit Project</x-slot>
    <x-slot name="title">Edit Project</x-slot>

    <div class="max-w-2xl">
        <x-ui.card>
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf
                @method('PUT')
                @include('projects._form', ['project' => $project])
                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete this project and all its tasks?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-rose-500 hover:text-rose-700">Delete Project</button>
                    </form>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Save Changes</button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
