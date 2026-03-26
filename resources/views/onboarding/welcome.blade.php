<x-onboarding-layout>
    <x-slot name="title">Welcome</x-slot>

    <x-ui.card>
        <div class="text-center py-6">
            {{-- Level badge --}}
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 mx-auto mb-4 flex items-center justify-center shadow-lg shadow-indigo-200">
                <span class="text-3xl font-bold text-white">1</span>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mb-1">Your Journey Begins</h2>
            <p class="text-sm text-gray-500 mb-4">You are Level 1 — {{ ucfirst(Auth::user()->rank ?? 'Initiate') }}</p>

            <div class="max-w-xs mx-auto mb-6">
                <x-ui.rank-badge :rank="Auth::user()->rank ?? 'initiate'" size="md" />
            </div>

            <p class="text-sm text-gray-500 mb-6">Every task you complete builds your character.<br>Every goal you achieve levels you up.</p>

            <a href="{{ route('dashboard') }}"
               class="inline-flex px-6 py-3 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                Go to Dashboard
            </a>
        </div>
    </x-ui.card>
</x-onboarding-layout>
