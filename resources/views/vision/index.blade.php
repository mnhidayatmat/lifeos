<x-app-layout>
    <x-slot name="header">Vision &amp; Identity</x-slot>
    <x-slot name="title">Vision &amp; Identity</x-slot>

    <div class="max-w-3xl space-y-6">

        {{-- Vision Statement --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Vision Statement</h2>
                <button type="button"
                        @click="$dispatch('open-modal-edit-vision')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    Edit
                </button>
            </div>

            @if($vision && $vision->vision_statement)
                <blockquote class="border-l-4 border-indigo-400 dark:border-indigo-600 pl-4 py-2 text-gray-700 dark:text-gray-300 italic leading-relaxed">
                    {{ $vision->vision_statement }}
                </blockquote>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">No vision statement yet. Define the future you are building toward.</p>
            @endif

            @if($vision && $vision->anti_vision)
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-rose-600 dark:text-rose-400 mb-1">Anti-Vision</h3>
                    <p class="text-sm text-rose-500/80 dark:text-rose-400/70 leading-relaxed">{{ $vision->anti_vision }}</p>
                </div>
            @endif
        </x-ui.card>

        {{-- Edit Vision Modal --}}
        <x-ui.modal name="edit-vision" maxWidth="lg">
            <form action="{{ route('vision.update-statement') }}" method="POST" class="p-6">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Vision Statement</h3>

                <div class="space-y-4">
                    <div>
                        <label for="vision_statement" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vision Statement</label>
                        <textarea name="vision_statement" id="vision_statement" rows="4"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="Describe the future you are building toward...">{{ $vision?->vision_statement }}</textarea>
                    </div>
                    <div>
                        <label for="anti_vision" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anti-Vision</label>
                        <textarea name="anti_vision" id="anti_vision" rows="3"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="What does the life you refuse to live look like?">{{ $vision?->anti_vision }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close-modal-edit-vision')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                        Save
                    </button>
                </div>
            </form>
        </x-ui.modal>

        {{-- I Am Statements --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">I Am Statements</h2>
                <button type="button"
                        @click="$dispatch('open-modal-edit-iam')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    Edit
                </button>
            </div>

            @if($vision && is_array($vision->i_am_statements) && count($vision->i_am_statements) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($vision->i_am_statements as $statement)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                            I am {{ $statement }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">No statements yet. Define who you are becoming.</p>
            @endif
        </x-ui.card>

        {{-- Edit I Am Statements Modal --}}
        <x-ui.modal name="edit-iam" maxWidth="lg">
            <form action="{{ route('vision.update-iam') }}" method="POST" class="p-6">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit I Am Statements</h3>

                <div>
                    <label for="statements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statements (one per line)</label>
                    <textarea name="statements" id="statements" rows="6"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                              placeholder="disciplined&#10;a lifelong learner&#10;focused on growth">{{ $vision && is_array($vision->i_am_statements) ? implode("\n", $vision->i_am_statements) : '' }}</textarea>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Each line becomes an "I am..." statement.</p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close-modal-edit-iam')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                        Save
                    </button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Identity Traits --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Identity Traits</h2>
                <button type="button"
                        @click="$dispatch('open-modal-add-trait')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    Add Trait
                </button>
            </div>

            @if($traits->count() > 0)
                <div class="space-y-3">
                    @foreach($traits as $identityTrait)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                <span class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ $identityTrait->trait }}</span>
                                @if($identityTrait->linked_stat)
                                    <x-ui.badge color="purple">{{ ucfirst($identityTrait->linked_stat) }}</x-ui.badge>
                                @endif
                                @php
                                    $statusColor = match($identityTrait->status) {
                                        'aspirational' => 'amber',
                                        'developing' => 'blue',
                                        'integrated' => 'emerald',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-ui.badge :color="$statusColor">{{ ucfirst($identityTrait->status) }}</x-ui.badge>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                {{-- Status change --}}
                                <form action="{{ route('vision.update-trait', $identityTrait) }}" method="POST" class="flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            onchange="this.form.submit()"
                                            class="text-xs rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 py-1 pl-2 pr-7 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="aspirational" @selected($identityTrait->status === 'aspirational')>Aspirational</option>
                                        <option value="developing" @selected($identityTrait->status === 'developing')>Developing</option>
                                        <option value="integrated" @selected($identityTrait->status === 'integrated')>Integrated</option>
                                    </select>
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('vision.destroy-trait', $identityTrait) }}" method="POST"
                                      onsubmit="return confirm('Remove this trait?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1 text-gray-400 hover:text-rose-500 dark:text-gray-500 dark:hover:text-rose-400 transition-colors"
                                            aria-label="Delete trait">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">No identity traits yet. Define the traits that shape who you are.</p>
            @endif
        </x-ui.card>

        {{-- Add Trait Modal --}}
        <x-ui.modal name="add-trait" maxWidth="lg">
            <form action="{{ route('vision.store-trait') }}" method="POST" class="p-6">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Identity Trait</h3>

                <div class="space-y-4">
                    <div>
                        <label for="trait" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trait</label>
                        <input type="text" name="trait" id="trait" required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               placeholder="e.g. Disciplined, Creative, Resilient">
                    </div>

                    <div>
                        <label for="linked_stat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Linked Stat</label>
                        <select name="linked_stat" id="linked_stat"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">None</option>
                            @foreach(\App\Models\User::STATS as $stat)
                                <option value="{{ $stat }}">{{ ucfirst($stat) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" id="status" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="aspirational">Aspirational</option>
                            <option value="developing">Developing</option>
                            <option value="integrated">Integrated</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close-modal-add-trait')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                        Add Trait
                    </button>
                </div>
            </form>
        </x-ui.modal>

    </div>
</x-app-layout>
