<x-app-layout>
    <x-slot name="header">Life Areas</x-slot>
    <x-slot name="title">Life Areas</x-slot>

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm text-gray-500">Organise your life into meaningful categories. Each area maps to character stats.</p>
            </div>
            @if($areas->count() < 10)
                <button @click="$dispatch('open-modal-create-area')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                    <x-icon name="plus" class="w-4 h-4" />
                    Add Area
                </button>
            @endif
        </div>

        {{-- Active Areas --}}
        @php $activeAreas = $areas->where('is_active', true); @endphp
        @php $inactiveAreas = $areas->where('is_active', false); @endphp

        @if($activeAreas->isEmpty() && $inactiveAreas->isEmpty())
            <x-ui.card>
                <x-ui.empty-state
                    icon="grid"
                    title="No life areas yet"
                    description="Create your first life area to start organising your goals."
                    action="Add Life Area"
                />
            </x-ui.card>
        @else
            <div class="space-y-3">
                @foreach($activeAreas as $area)
                    <x-ui.card>
                        <div x-data="{ editing: false }" class="flex items-center gap-4">
                            {{-- Color dot --}}
                            <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $area->color }}"></div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $area->name }}</h3>
                                    @if($area->is_preset)
                                        <x-ui.badge color="gray" size="xs">Preset</x-ui.badge>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-gray-500">
                                        <span class="font-medium">{{ ucfirst($area->primary_stat) }}</span> (primary)
                                    </span>
                                    <span class="text-xs text-gray-400">+</span>
                                    <span class="text-xs text-gray-500">
                                        <span class="font-medium">{{ ucfirst($area->secondary_stat) }}</span> (secondary)
                                    </span>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="hidden sm:flex items-center gap-4 text-xs text-gray-500">
                                <span>{{ $area->goals()->count() }} goals</span>
                                <span>{{ $area->projects()->count() }} projects</span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1">
                                <button @click="$dispatch('open-modal-edit-area-{{ $area->id }}')"
                                        class="p-1.5 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100">
                                    <x-icon name="settings" class="w-4 h-4" />
                                </button>
                                <form method="POST" action="{{ route('life-areas.toggle', $area) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-md hover:bg-gray-100" title="Deactivate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Edit Modal --}}
                    <x-ui.modal name="edit-area-{{ $area->id }}">
                        <form method="POST" action="{{ route('life-areas.update', $area) }}" class="p-6">
                            @csrf
                            @method('PUT')
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Edit Life Area</h2>
                            @include('life-areas._form', ['area' => $area])
                            <div class="flex items-center justify-between mt-6">
                                <button type="button" @click="$dispatch('close-modal-edit-area-{{ $area->id }}')" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                                <div class="flex items-center gap-3">
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </x-ui.modal>
                @endforeach
            </div>

            {{-- Inactive Areas --}}
            @if($inactiveAreas->isNotEmpty())
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Inactive</h3>
                    <div class="space-y-2">
                        @foreach($inactiveAreas as $area)
                            <x-ui.card>
                                <div class="flex items-center gap-4 opacity-50">
                                    <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $area->color }}"></div>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-600">{{ $area->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <form method="POST" action="{{ route('life-areas.toggle', $area) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-emerald-600 rounded-md hover:bg-gray-100" title="Activate">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('life-areas.destroy', $area) }}" onsubmit="return confirm('Delete this life area? All linked goals and projects will also be removed.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-md hover:bg-gray-100" title="Delete">
                                                <x-icon name="x-mark" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </x-ui.card>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Create Modal --}}
    <x-ui.modal name="create-area">
        <form method="POST" action="{{ route('life-areas.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Create Life Area</h2>
            @include('life-areas._form', ['area' => null])
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="$dispatch('close-modal-create-area')" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
