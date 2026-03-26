<x-onboarding-layout>
    <x-slot name="title">Your Life Areas</x-slot>

    <x-ui.card>
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Your Life Areas</h2>
            <p class="text-sm text-gray-500 mt-1">We've set up these areas based on your archetype. You can customise them later.</p>
        </div>

        <div class="space-y-2 mb-6">
            @foreach($areas as $area)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $area->color }}"></div>
                    <span class="text-sm font-medium text-gray-900 flex-1">{{ $area->name }}</span>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span>{{ ucfirst($area->primary_stat) }}</span>
                        <span class="text-gray-300">+</span>
                        <span>{{ ucfirst($area->secondary_stat) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('onboarding.store-areas') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-3 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                Looks Good — Continue
            </button>
        </form>
    </x-ui.card>
</x-onboarding-layout>
