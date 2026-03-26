<x-app-layout>
    <x-slot name="header">Create Goal</x-slot>
    <x-slot name="title">Create Goal</x-slot>

    <div class="max-w-2xl">
        <x-ui.card>
            <form method="POST" action="{{ route('goals.store') }}">
                @csrf
                @include('goals._form', ['goal' => null])
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('goals.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Create Goal</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
