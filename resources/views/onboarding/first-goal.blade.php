<x-onboarding-layout>
    <x-slot name="title">Your First Goal</x-slot>

    <x-ui.card>
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Set Your First Goal</h2>
            <p class="text-sm text-gray-500 mt-1">What's one thing you want to achieve? You can add more goals later.</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store-first-goal') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Goal</label>
                    <input type="text" name="title" id="title"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="e.g., Complete my thesis, Launch MVP, Get fit" required>
                    @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="life_area_id" class="block text-sm font-medium text-gray-700 mb-1">Life Area</label>
                    <select name="life_area_id" id="life_area_id"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full mt-6 px-4 py-3 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                Create Goal & Start
            </button>
        </form>
    </x-ui.card>
</x-onboarding-layout>
