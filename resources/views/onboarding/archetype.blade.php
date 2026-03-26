<x-onboarding-layout>
    <x-slot name="title">Choose Your Path</x-slot>

    <x-ui.card>
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Who are you?</h2>
            <p class="text-sm text-gray-500 mt-1">Choose the archetype that best describes you. This sets up your starting life areas.</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store-archetype') }}">
            @csrf
            <div class="space-y-3">
                @php
                    $archetypes = [
                        'student' => ['label' => 'Student', 'desc' => 'Focused on learning, research, and personal growth'],
                        'researcher' => ['label' => 'Researcher', 'desc' => 'Deep knowledge work, publishing, and academic goals'],
                        'founder' => ['label' => 'Founder', 'desc' => 'Building a business, managing finances, and scaling'],
                        'professional' => ['label' => 'Professional', 'desc' => 'Career growth, work-life balance, and family'],
                        'creator' => ['label' => 'Creator', 'desc' => 'Creative projects, personal brand, and self-expression'],
                    ];
                @endphp

                @foreach($archetypes as $key => $archetype)
                    <label class="block">
                        <input type="radio" name="archetype" value="{{ $key }}" class="peer sr-only" @checked($loop->first)>
                        <div class="p-4 rounded-xl border-2 border-gray-200 cursor-pointer
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                                    hover:border-gray-300 transition-colors">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $archetype['label'] }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $archetype['desc'] }}</p>
                        </div>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="w-full mt-6 px-4 py-3 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                Continue
            </button>
        </form>
    </x-ui.card>
</x-onboarding-layout>
