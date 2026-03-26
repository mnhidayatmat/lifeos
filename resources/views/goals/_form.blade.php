<div class="space-y-4">
    {{-- Title --}}
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $goal?->title) }}"
               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required
               placeholder="e.g., Publish 3 research papers this year">
        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
        <textarea name="description" id="description" rows="2"
                  class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Why does this goal matter?">{{ old('description', $goal?->description) }}</textarea>
    </div>

    {{-- Life Area --}}
    <div>
        <label for="life_area_id" class="block text-sm font-medium text-gray-700 mb-1">Life Area</label>
        <select name="life_area_id" id="life_area_id"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Select a life area</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" @selected(old('life_area_id', $goal?->life_area_id) == $area->id)>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>
        @error('life_area_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Progress Type --}}
        <div>
            <label for="progress_type" class="block text-sm font-medium text-gray-700 mb-1">Progress Tracking</label>
            <select name="progress_type" id="progress_type" x-data x-on:change="$dispatch('progress-type-changed', { type: $event.target.value })"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="task_based" @selected(old('progress_type', $goal?->progress_type) === 'task_based')>Task-based (automatic)</option>
                <option value="kpi_based" @selected(old('progress_type', $goal?->progress_type) === 'kpi_based')>KPI / Metric target</option>
                <option value="manual" @selected(old('progress_type', $goal?->progress_type) === 'manual')>Manual percentage</option>
            </select>
        </div>

        {{-- Target Value (for KPI) --}}
        <div x-data="{ type: '{{ old('progress_type', $goal?->progress_type ?? 'task_based') }}' }"
             x-on:progress-type-changed.window="type = $event.detail.type"
             x-show="type === 'kpi_based'" x-cloak>
            <label for="target_value" class="block text-sm font-medium text-gray-700 mb-1">Target Value</label>
            <input type="number" name="target_value" id="target_value" step="0.01" min="0"
                   value="{{ old('target_value', $goal?->target_value) }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                   placeholder="e.g., 3">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Priority --}}
        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
            <select name="priority" id="priority"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="low" @selected(old('priority', $goal?->priority) === 'low')>Low</option>
                <option value="medium" @selected(old('priority', $goal?->priority ?? 'medium') === 'medium')>Medium</option>
                <option value="high" @selected(old('priority', $goal?->priority) === 'high')>High</option>
                <option value="urgent" @selected(old('priority', $goal?->priority) === 'urgent')>Urgent</option>
            </select>
        </div>

        {{-- Due Date --}}
        <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-gray-400">(optional)</span></label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $goal?->due_date?->format('Y-m-d')) }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
</div>
