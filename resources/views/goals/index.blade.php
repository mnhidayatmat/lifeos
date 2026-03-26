<x-app-layout>
    <x-slot name="header">Goals</x-slot>
    <x-slot name="title">Goals</x-slot>

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                {{-- Area filter tabs --}}
                <a href="{{ route('goals.index') }}"
                   class="px-3 py-1.5 text-sm rounded-lg {{ !$areaFilter ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:bg-gray-100' }}">
                    All
                </a>
                @foreach($areas as $area)
                    <a href="{{ route('goals.index', ['area' => $area->id]) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $areaFilter == $area->id ? 'font-medium' : 'text-gray-500 hover:bg-gray-100' }}"
                       style="{{ $areaFilter == $area->id ? 'background-color: ' . $area->color . '15; color: ' . $area->color : '' }}">
                        {{ $area->name }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('goals.create') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                New Goal
            </a>
        </div>

        {{-- Goals list --}}
        @if($goals->isEmpty())
            <x-ui.card>
                <x-ui.empty-state
                    icon="target"
                    title="No goals yet"
                    description="Set your first goal to give your tasks purpose and direction."
                    action="Create Goal"
                    :actionUrl="route('goals.create')"
                />
            </x-ui.card>
        @else
            <div class="space-y-3">
                @foreach($goals as $goal)
                    <a href="{{ route('goals.show', $goal) }}" class="block">
                        <x-ui.card class="hover:border-gray-300 transition-colors">
                            <div class="flex items-start gap-4">
                                {{-- Area color --}}
                                <div class="w-2 h-full min-h-[3rem] rounded-full shrink-0 mt-0.5" style="background-color: {{ $goal->lifeArea->color }}"></div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if($goal->is_domino)
                                            <span class="text-amber-500 dark:text-amber-400" title="Domino Goal — Your #1 Priority">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                            </span>
                                        @endif
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $goal->title }}</h3>
                                        @php
                                            $statusColors = [
                                                'not_started' => 'gray',
                                                'in_progress' => 'blue',
                                                'on_hold' => 'amber',
                                                'completed' => 'emerald',
                                                'abandoned' => 'gray',
                                            ];
                                        @endphp
                                        <x-ui.badge :color="$statusColors[$goal->status] ?? 'gray'" size="xs">
                                            {{ str_replace('_', ' ', ucfirst($goal->status)) }}
                                        </x-ui.badge>
                                    </div>

                                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                                        <span style="color: {{ $goal->lifeArea->color }}">{{ $goal->lifeArea->name }}</span>
                                        @if($goal->due_date)
                                            <span class="{{ $goal->due_date->isPast() && $goal->status !== 'completed' ? 'text-rose-500' : '' }}">
                                                Due {{ $goal->due_date->format('M j, Y') }}
                                            </span>
                                        @endif
                                        <span>{{ ucfirst($goal->priority) }}</span>
                                    </div>

                                    {{-- Progress --}}
                                    <x-ui.progress-bar :value="$goal->progress" color="indigo" size="sm" />
                                </div>

                                {{-- Progress number --}}
                                <div class="text-right shrink-0">
                                    <span class="text-lg font-bold text-gray-900">{{ $goal->progress }}%</span>
                                </div>
                            </div>
                        </x-ui.card>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
