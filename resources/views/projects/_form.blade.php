<div class="space-y-4">
    <div>
        <label for="project_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" name="title" id="project_title" value="{{ old('title', $project?->title) }}"
               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required
               placeholder="e.g., Paper revision for ICML submission">
        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="project_description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
        <textarea name="description" id="project_description" rows="2"
                  class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $project?->description) }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="project_life_area_id" class="block text-sm font-medium text-gray-700 mb-1">Life Area</label>
            <select name="life_area_id" id="project_life_area_id"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">Select area</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" @selected(old('life_area_id', $project?->life_area_id) == $area->id)>{{ $area->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="project_goal_id" class="block text-sm font-medium text-gray-700 mb-1">Goal <span class="text-gray-400">(optional)</span></label>
            <select name="goal_id" id="project_goal_id"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">No goal</option>
                @foreach($goals as $goal)
                    <option value="{{ $goal->id }}" @selected(old('goal_id', $project?->goal_id) == $goal->id)>{{ $goal->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ICE Scoring --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ICE Score <span class="text-gray-400">(optional — Impact, Confidence, Ease: 1-10)</span></label>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <input type="number" name="impact_score" min="1" max="10" value="{{ old('impact_score', $project?->impact_score) }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Impact">
            </div>
            <div>
                <input type="number" name="confidence_score" min="1" max="10" value="{{ old('confidence_score', $project?->confidence_score) }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Confidence">
            </div>
            <div>
                <input type="number" name="ease_score" min="1" max="10" value="{{ old('ease_score', $project?->ease_score) }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ease">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="project_priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
            <select name="priority" id="project_priority"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="low" @selected(old('priority', $project?->priority) === 'low')>Low</option>
                <option value="medium" @selected(old('priority', $project?->priority ?? 'medium') === 'medium')>Medium</option>
                <option value="high" @selected(old('priority', $project?->priority) === 'high')>High</option>
                <option value="urgent" @selected(old('priority', $project?->priority) === 'urgent')>Urgent</option>
            </select>
        </div>
        <div>
            <label for="project_due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-gray-400">(optional)</span></label>
            <input type="date" name="due_date" id="project_due_date" value="{{ old('due_date', $project?->due_date?->format('Y-m-d')) }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
</div>
