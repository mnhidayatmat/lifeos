<x-app-layout>
    <x-slot name="header">Important Dates</x-slot>
    <x-slot name="title">Important Dates</x-slot>

    @php
        $reminderChoices = [
            30 => '1 month',
            14 => '2 weeks',
            7  => '1 week',
            3  => '3 days',
            1  => '1 day',
            0  => 'Day of',
        ];
        $buckets = [
            'overdue'    => ['label' => 'Overdue',     'tone' => 'rose'],
            'today'      => ['label' => 'Today',       'tone' => 'teal'],
            'this_week'  => ['label' => 'This week',   'tone' => 'amber'],
            'this_month' => ['label' => 'This month',  'tone' => 'blue'],
            'later'      => ['label' => 'Later',       'tone' => 'gray'],
            'completed'  => ['label' => 'Done',        'tone' => 'emerald'],
        ];
        $toneClasses = [
            'rose'    => 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-400',
            'teal'    => 'bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-400',
            'amber'   => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
            'blue'    => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
            'gray'    => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
            'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
        ];
    @endphp

    <div class="max-w-3xl"
         x-data="importantDates({
            storeUrl: '{{ route('important-dates.store') }}',
            openOnLoad: {{ $errors->any() ? 'true' : 'false' }},
            old: {{ Js::from(old()) }},
         })">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 mb-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $upcomingCount }} upcoming {{ Str::plural('date', $upcomingCount) }}
            </p>
            <div class="flex items-center gap-2">
                <button @click="$dispatch('open-modal-calendar-sync')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="calendar" class="w-4 h-4" />
                    Sync calendar
                </button>
                <button @click="openCreate()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors">
                    <x-icon name="plus" class="w-4 h-4" />
                    Add date
                </button>
            </div>
        </div>

        @if($dates->isEmpty())
            <x-ui.card>
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                        <x-icon name="calendar" class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No important dates yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">Track deadlines that matter — renewals, submissions, exams, anniversaries — and get reminded before they arrive.</p>
                    <button @click="openCreate()"
                            class="mt-4 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900 transition-colors">
                        <x-icon name="plus" class="w-4 h-4" />
                        Add date
                    </button>
                </div>
            </x-ui.card>
        @else
            <div class="space-y-6">
                @foreach($buckets as $key => $meta)
                    @php $items = $grouped[$key] ?? collect(); @endphp
                    @continue($items->isEmpty())

                    <section>
                        <div class="flex items-center gap-2 mb-2">
                            <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ $meta['label'] }}</h2>
                            <span class="text-xs text-gray-300 dark:text-gray-600">{{ $items->count() }}</span>
                        </div>

                        <div class="space-y-1.5">
                            @foreach($items as $item)
                                @php $done = $item->isCompleted(); @endphp
                                <x-ui.card :padding="false" class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        {{-- Done toggle --}}
                                        <form method="POST" action="{{ route('important-dates.toggle', $item) }}" class="pt-0.5">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors
                                                        {{ $done
                                                            ? 'bg-emerald-500 border-emerald-500 dark:bg-emerald-600 dark:border-emerald-600'
                                                            : 'border-gray-300 dark:border-gray-600 hover:border-emerald-400 dark:hover:border-emerald-500' }}"
                                                    aria-label="{{ $done ? 'Reopen' : 'Mark as done' }}: {{ $item->title }}">
                                                @if($done)
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm font-medium {{ $done ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-gray-100' }}">
                                                    {{ $item->title }}
                                                </span>
                                                @if($item->recurrence)
                                                    <span title="Repeats {{ $item->recurrence }}">
                                                        <x-icon name="repeat" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-1 flex items-center gap-2 flex-wrap text-xs text-gray-500 dark:text-gray-400">
                                                <span class="inline-flex items-center gap-1">
                                                    <x-icon name="calendar" class="w-3.5 h-3.5" />
                                                    {{ $item->nextOccurrence()->format('D, M j, Y') }}@if(! $item->all_day) · {{ \Carbon\Carbon::parse($item->time)->format('g:i A') }}@endif
                                                </span>
                                                @unless($done)
                                                    <span class="px-1.5 py-0.5 rounded font-medium {{ $toneClasses[$meta['tone']] }}">
                                                        {{ $item->countdownLabel() }}
                                                    </span>
                                                @endunless
                                                @if($item->lifeArea)
                                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                                        {{ $item->lifeArea->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($item->reminderOffsets()->isNotEmpty() && ! $done)
                                                <div class="mt-1.5 flex items-center gap-1 flex-wrap">
                                                    <x-icon name="bell" class="w-3 h-3 text-gray-300 dark:text-gray-600" />
                                                    @foreach($item->reminderOffsets() as $offset)
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-50 dark:bg-gray-800/60 text-gray-400 dark:text-gray-500">
                                                            {{ $reminderChoices[$offset] ?? $offset.'d' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-1 pt-0.5">
                                            <button type="button"
                                                    @click='openEdit(@json([
                                                        "id" => $item->id,
                                                        "action" => route("important-dates.update", $item),
                                                        "title" => $item->title,
                                                        "description" => $item->description,
                                                        "date" => $item->date->format("Y-m-d"),
                                                        "time" => $item->time ? \Carbon\Carbon::parse($item->time)->format("H:i") : "",
                                                        "recurrence" => $item->recurrence,
                                                        "life_area_id" => $item->life_area_id,
                                                        "reminders" => $item->reminderOffsets()->values(),
                                                    ]))'
                                                    class="text-gray-300 dark:text-gray-600 hover:text-teal-500 dark:hover:text-teal-400 transition-colors"
                                                    aria-label="Edit {{ $item->title }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('important-dates.destroy', $item) }}"
                                                  onsubmit="return confirm('Delete this date? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-gray-300 dark:text-gray-600 hover:text-rose-500 dark:hover:text-rose-400 transition-colors"
                                                        aria-label="Delete {{ $item->title }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </x-ui.card>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif

        {{-- Create / Edit Modal --}}
        <x-ui.modal name="date-form" maxWidth="lg">
            <form :action="form.action" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="_method" :value="form.id ? 'PUT' : 'POST'">

                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4"
                    x-text="form.id ? 'Edit important date' : 'Add important date'"></h2>

                <div class="space-y-4">
                    {{-- Title --}}
                    <div>
                        <label for="date-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" name="title" id="date-title" required x-model="form.title"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-teal-500 focus:ring-teal-500"
                               placeholder="e.g. Passport renewal deadline">
                    </div>

                    {{-- Date + Time --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="date-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" name="date" id="date-date" required x-model="form.date"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-teal-500 focus:ring-teal-500">
                        </div>
                        <div>
                            <label for="date-time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Time <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <input type="time" name="time" id="date-time" x-model="form.time"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-teal-500 focus:ring-teal-500">
                        </div>
                    </div>

                    {{-- Reminders --}}
                    <fieldset>
                        <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remind me before</legend>
                        <div class="flex flex-wrap gap-2">
                            @foreach($reminderChoices as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="reminders[]" value="{{ $value }}" class="sr-only peer"
                                           x-model="form.reminders">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-xs font-medium transition-colors
                                                 border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400
                                                 peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700
                                                 dark:peer-checked:bg-teal-950 dark:peer-checked:text-teal-300 dark:peer-checked:border-teal-600">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Reminders appear in your synced calendar and in-app notifications.</p>
                    </fieldset>

                    {{-- Repeat + Life Area --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="date-recurrence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Repeat</label>
                            <select name="recurrence" id="date-recurrence" x-model="form.recurrence"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">Never</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label for="date-area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Life Area</label>
                            <select name="life_area_id" id="date-area" x-model="form.life_area_id"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">None</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="date-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Notes <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea name="description" id="date-description" rows="2" x-model="form.description"
                                  class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-teal-500 focus:ring-teal-500"
                                  placeholder="Any details to remember"></textarea>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mt-4 p-3 rounded-lg bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800">
                        <ul class="text-sm text-rose-600 dark:text-rose-400 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" @click="$dispatch('close-modal-date-form')"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors"
                            x-text="form.id ? 'Save changes' : 'Add date'"></button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Calendar Sync Modal --}}
        <x-ui.modal name="calendar-sync" maxWidth="lg">
            <div class="p-6" x-data="{ copied: false, url: @js($feedUrl) }">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Sync to your calendar</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Subscribe to this private link once in Google, Apple, or Outlook Calendar. Every important date and its reminders show up automatically and stay up to date.
                </p>

                <div class="flex items-stretch gap-2 mb-4">
                    <input type="text" readonly :value="url"
                           class="flex-1 min-w-0 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs text-gray-600 dark:text-gray-300 font-mono"
                           @focus="$event.target.select()">
                    <button type="button"
                            @click="navigator.clipboard.writeText(url); copied = true; setTimeout(() => copied = false, 2000)"
                            class="shrink-0 px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors">
                        <span x-show="!copied">Copy</span>
                        <span x-show="copied" x-cloak>Copied!</span>
                    </button>
                </div>

                <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-4 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <p class="font-medium text-gray-700 dark:text-gray-300">How to subscribe</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li><span class="font-medium">Google Calendar:</span> Other calendars → + → From URL → paste the link.</li>
                        <li><span class="font-medium">Apple Calendar:</span> File → New Calendar Subscription → paste the link.</li>
                        <li><span class="font-medium">Outlook:</span> Add calendar → Subscribe from web → paste the link.</li>
                    </ul>
                    <p class="text-xs text-gray-400 dark:text-gray-500 pt-1">Keep this link private — anyone with it can view your dates. Calendar apps refresh subscriptions periodically (often every few hours).</p>
                </div>

                <div class="flex justify-end mt-5">
                    <button type="button" @click="$dispatch('close-modal-calendar-sync')"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                        Done
                    </button>
                </div>
            </div>
        </x-ui.modal>
    </div>

    <script>
        function importantDates(config) {
            const blank = () => ({
                id: null,
                action: config.storeUrl,
                title: '',
                description: '',
                date: '',
                time: '',
                recurrence: '',
                life_area_id: '',
                reminders: ['7', '1'],
            });

            return {
                form: blank(),

                init() {
                    // Re-open the form with submitted values when validation fails.
                    if (config.openOnLoad && config.old && config.old.title !== undefined) {
                        this.form = {
                            ...blank(),
                            title: config.old.title ?? '',
                            description: config.old.description ?? '',
                            date: config.old.date ?? '',
                            time: config.old.time ?? '',
                            recurrence: config.old.recurrence ?? '',
                            life_area_id: config.old.life_area_id ?? '',
                            reminders: (config.old.reminders ?? []).map(String),
                        };
                        this.$nextTick(() => this.$dispatch('open-modal-date-form'));
                    }
                },

                openCreate() {
                    this.form = blank();
                    this.$dispatch('open-modal-date-form');
                },

                openEdit(data) {
                    this.form = {
                        id: data.id,
                        action: data.action,
                        title: data.title ?? '',
                        description: data.description ?? '',
                        date: data.date ?? '',
                        time: data.time ?? '',
                        recurrence: data.recurrence ?? '',
                        life_area_id: data.life_area_id ?? '',
                        reminders: (data.reminders ?? []).map(String),
                    };
                    this.$dispatch('open-modal-date-form');
                },
            };
        }
    </script>
</x-app-layout>
