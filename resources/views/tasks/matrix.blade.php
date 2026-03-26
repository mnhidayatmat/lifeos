<x-app-layout>
    <x-slot name="header">Tasks</x-slot>
    <x-slot name="title">Eisenhower Matrix</x-slot>

    <div>
        {{-- Header (same tabs as task index) --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-1">
                @foreach(['today' => 'Today', 'overdue' => 'Overdue', 'all' => 'All', 'matrix' => 'Matrix'] as $key => $label)
                    <a href="{{ route('tasks.index', ['view' => $key]) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $view === $key ? 'bg-indigo-50 text-indigo-700 font-medium dark:bg-indigo-950 dark:text-indigo-400' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                        {{ $label }}
                        @if($key === 'overdue')
                            @php $overdueCount = auth()->user()->tasks()->where('due_date', '<', today())->whereNotIn('status', ['completed','cancelled'])->count(); @endphp
                            @if($overdueCount > 0)
                                <span class="ml-1 px-1.5 py-0.5 text-[10px] bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-400 rounded-full">{{ $overdueCount }}</span>
                            @endif
                        @endif
                    </a>
                @endforeach
            </div>
            <button @click="$dispatch('open-modal-create-task')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                New Task
            </button>
        </div>

        {{-- Axis labels --}}
        <div class="mb-2 flex items-center justify-center">
            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Urgent</span>
            <span class="mx-4 text-gray-300 dark:text-gray-700">&larr;</span>
            <span class="text-[10px] text-gray-300 dark:text-gray-600">URGENCY</span>
            <span class="mx-4 text-gray-300 dark:text-gray-700">&rarr;</span>
            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Not Urgent</span>
        </div>

        {{-- Matrix Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Q1: Do First (Urgent + Important) --}}
            <div class="rounded-xl border-2 border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/20 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                    <h3 class="text-sm font-bold text-rose-700 dark:text-rose-400">Do First</h3>
                    <span class="text-xs text-rose-400 dark:text-rose-500">Urgent & Important</span>
                    <span class="ml-auto text-xs font-medium text-rose-500 dark:text-rose-400 bg-rose-100 dark:bg-rose-900/30 px-1.5 py-0.5 rounded">{{ $matrix['do_first']->count() }}</span>
                </div>
                @include('tasks._matrix-quadrant', ['tasks' => $matrix['do_first'], 'emptyText' => 'No critical tasks right now.'])
            </div>

            {{-- Q2: Schedule (Not Urgent + Important) --}}
            <div class="rounded-xl border-2 border-blue-200 dark:border-blue-900/50 bg-blue-50/50 dark:bg-blue-950/20 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <h3 class="text-sm font-bold text-blue-700 dark:text-blue-400">Schedule</h3>
                    <span class="text-xs text-blue-400 dark:text-blue-500">Not Urgent & Important</span>
                    <span class="ml-auto text-xs font-medium text-blue-500 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-1.5 py-0.5 rounded">{{ $matrix['schedule']->count() }}</span>
                </div>
                @include('tasks._matrix-quadrant', ['tasks' => $matrix['schedule'], 'emptyText' => 'Plan important work here.'])
            </div>

            {{-- Q3: Delegate (Urgent + Not Important) --}}
            <div class="rounded-xl border-2 border-amber-200 dark:border-amber-900/50 bg-amber-50/50 dark:bg-amber-950/20 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                    <h3 class="text-sm font-bold text-amber-700 dark:text-amber-400">Delegate</h3>
                    <span class="text-xs text-amber-400 dark:text-amber-500">Urgent & Not Important</span>
                    <span class="ml-auto text-xs font-medium text-amber-500 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30 px-1.5 py-0.5 rounded">{{ $matrix['delegate']->count() }}</span>
                </div>
                @include('tasks._matrix-quadrant', ['tasks' => $matrix['delegate'], 'emptyText' => 'Tasks to hand off or batch.'])
            </div>

            {{-- Q4: Eliminate (Not Urgent + Not Important) --}}
            <div class="rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-gray-400 dark:bg-gray-600"></div>
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400">Eliminate</h3>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Not Urgent & Not Important</span>
                    <span class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ $matrix['eliminate']->count() }}</span>
                </div>
                @include('tasks._matrix-quadrant', ['tasks' => $matrix['eliminate'], 'emptyText' => 'Consider dropping these.'])
            </div>
        </div>

        {{-- Legend --}}
        <div class="mt-4 flex items-center justify-center gap-6 text-xs text-gray-400 dark:text-gray-500">
            <span><strong class="text-gray-600 dark:text-gray-300">Urgent</strong> = High or Urgent priority</span>
            <span><strong class="text-gray-600 dark:text-gray-300">Important</strong> = marked via checkbox on task</span>
        </div>
    </div>

    {{-- Create Task Modal (reuse) --}}
    <x-ui.modal name="create-task" maxWidth="lg">
        <form method="POST" action="{{ route('tasks.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create Task</h2>
            @include('tasks._form', ['task' => null])
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="$dispatch('close-modal-create-task')" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
