<x-app-layout>
    <x-slot name="header">Habits</x-slot>
    <x-slot name="title">Habits</x-slot>

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $habits->count() }} {{ Str::plural('habit', $habits->count()) }} tracked
            </p>
            <button @click="$dispatch('open-modal-create-habit')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                Add Habit
            </button>
        </div>

        @if($habits->isEmpty())
            <x-ui.card>
                <x-ui.empty-state
                    icon="check-square"
                    title="No habits yet"
                    description="Build consistency by tracking daily habits. Start with something small."
                    action="Add Habit"
                />
            </x-ui.card>
        @else
            @php
                $routines = [
                    'morning' => [
                        'label' => 'Morning',
                        'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z',
                        'color' => 'amber',
                    ],
                    'afternoon' => [
                        'label' => 'Afternoon',
                        'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z',
                        'color' => 'blue',
                    ],
                    'evening' => [
                        'label' => 'Evening',
                        'icon' => 'M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z',
                        'color' => 'purple',
                    ],
                ];
            @endphp

            <div class="space-y-6">
                @foreach($routines as $routineKey => $routine)
                    @php $routineHabits = $grouped[$routineKey] ?? collect(); @endphp

                    <section>
                        {{-- Section heading --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-lg bg-{{ $routine['color'] }}-50 dark:bg-{{ $routine['color'] }}-950 flex items-center justify-center">
                                <svg class="w-4 h-4 text-{{ $routine['color'] }}-500 dark:text-{{ $routine['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $routine['icon'] }}"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $routine['label'] }}</h2>
                            @if($routineHabits->isNotEmpty())
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $routineHabits->filter(fn($h) => $h->isCompletedToday())->count() }}/{{ $routineHabits->count() }}
                                </span>
                            @endif
                        </div>

                        @if($routineHabits->isEmpty())
                            <x-ui.card>
                                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                                    No {{ $routineKey }} habits yet
                                </p>
                            </x-ui.card>
                        @else
                            <div class="space-y-1">
                                @foreach($routineHabits as $habit)
                                    @php $completed = $habit->isCompletedToday(); @endphp
                                    <x-ui.card :padding="false" class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            {{-- Toggle checkbox --}}
                                            <form method="POST" action="{{ route('habits.toggle', $habit) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors
                                                            {{ $completed
                                                                ? 'bg-indigo-500 border-indigo-500 dark:bg-indigo-600 dark:border-indigo-600'
                                                                : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500' }}"
                                                        aria-label="{{ $completed ? 'Mark as incomplete' : 'Mark as complete' }}: {{ $habit->title }}">
                                                    @if($completed)
                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>

                                            {{-- Habit title --}}
                                            <span class="flex-1 min-w-0 text-sm {{ $completed
                                                ? 'text-gray-400 dark:text-gray-500 line-through'
                                                : 'text-gray-900 dark:text-gray-100' }}">
                                                {{ $habit->title }}
                                            </span>

                                            {{-- Effort badge --}}
                                            <x-ui.effort-badge :effort="$habit->effort" />

                                            {{-- Streak --}}
                                            @if($habit->currentStreak() > 0)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400" title="Current streak">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12.356 2.082a.75.75 0 00-1.088.39L8.373 9.67a.75.75 0 00.71.98h2.635l-1.453 7.266a.75.75 0 001.342.545l6.324-8.883a.75.75 0 00-.613-1.178h-2.882l2.47-5.14a.75.75 0 00-.878-1.04l-4.671 1.862z"/>
                                                    </svg>
                                                    {{ $habit->currentStreak() }}
                                                </span>
                                            @endif

                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('habits.destroy', $habit) }}"
                                                  onsubmit="return confirm('Delete this habit? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-gray-300 dark:text-gray-600 hover:text-rose-500 dark:hover:text-rose-400 transition-colors"
                                                        aria-label="Delete habit: {{ $habit->title }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </x-ui.card>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Create Habit Modal --}}
    <x-ui.modal name="create-habit" maxWidth="lg">
        <form method="POST" action="{{ route('habits.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Habit</h2>

            <div class="space-y-4">
                {{-- Title --}}
                <div>
                    <label for="habit-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input type="text" name="title" id="habit-title" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="e.g. Meditate for 10 minutes">
                </div>

                {{-- Routine --}}
                <div>
                    <label for="habit-routine" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Routine</label>
                    <select name="routine" id="habit-routine" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                        <option value="evening">Evening</option>
                    </select>
                </div>

                {{-- Frequency --}}
                <div>
                    <label for="habit-frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frequency</label>
                    <select name="frequency" id="habit-frequency" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="daily">Daily</option>
                        <option value="weekdays">Weekdays</option>
                        <option value="weekends">Weekends</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                {{-- Effort --}}
                <fieldset>
                    <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Effort</legend>
                    <div class="flex items-center gap-3" x-data="{ effort: 'small' }">
                        @foreach(['small' => 'S', 'medium' => 'M', 'large' => 'L'] as $value => $label)
                            @php
                                $effortColors = [
                                    'small'  => 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 dark:border-emerald-600',
                                    'medium' => 'border-amber-500 bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400 dark:border-amber-600',
                                    'large'  => 'border-rose-500 bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-400 dark:border-rose-600',
                                ];
                            @endphp
                            <label class="relative cursor-pointer">
                                <input type="radio" name="effort" value="{{ $value }}" class="sr-only peer"
                                       x-model="effort" {{ $value === 'small' ? 'checked' : '' }}>
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 text-sm font-bold transition-colors
                                    border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 hover:border-gray-300 dark:hover:border-gray-600"
                                    :class="effort === '{{ $value }}' && '{{ $effortColors[$value] }}'">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Life Area --}}
                <div>
                    <label for="habit-area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Life Area</label>
                    <select name="life_area_id" id="habit-area"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">None</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mt-4 p-3 rounded-lg bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800">
                    <ul class="text-sm text-rose-600 dark:text-rose-400 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="$dispatch('close-modal-create-habit')"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                    Create Habit
                </button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
