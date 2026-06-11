<x-onboarding-layout>
    <x-slot name="title">Welcome</x-slot>

    <x-ui.card>
        <div class="text-center py-6">
            {{-- Check badge --}}
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 mx-auto mb-5 flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">You're all set</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                Your life areas and first goal are ready. Break goals into projects and tasks, and track your progress as you go.
            </p>

            <a href="{{ route('dashboard') }}"
               class="inline-flex px-6 py-3 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                Go to Dashboard
            </a>
        </div>
    </x-ui.card>
</x-onboarding-layout>
