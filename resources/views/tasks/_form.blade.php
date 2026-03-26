<div class="space-y-4">
    <div>
        <label for="task_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" name="title" id="task_title" value="{{ old('title', $task?->title) }}"
               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required
               placeholder="What needs to be done?">
        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="task_description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
        <textarea name="description" id="task_description" rows="2"
                  class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $task?->description) }}</textarea>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Effort</label>
            <div class="flex gap-2">
                @foreach(['small' => 'S (5 XP)', 'medium' => 'M (15 XP)', 'large' => 'L (30 XP)'] as $value => $label)
                    <label class="flex-1">
                        <input type="radio" name="effort" value="{{ $value }}" class="peer sr-only"
                               @checked(old('effort', $task?->effort ?? 'medium') === $value)>
                        <div class="text-center px-2 py-2 rounded-lg border-2 border-gray-200 text-xs font-medium cursor-pointer
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700
                                    hover:border-gray-300 transition-colors">
                            {{ $label }}
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label for="task_priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
            <select name="priority" id="task_priority"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="low" @selected(old('priority', $task?->priority) === 'low')>Low</option>
                <option value="medium" @selected(old('priority', $task?->priority ?? 'medium') === 'medium')>Medium</option>
                <option value="high" @selected(old('priority', $task?->priority) === 'high')>High</option>
                <option value="urgent" @selected(old('priority', $task?->priority) === 'urgent')>Urgent</option>
            </select>
        </div>
        <div>
            <label for="task_due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
            <input type="date" name="due_date" id="task_due_date" value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}"
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    {{-- Eisenhower: Important toggle --}}
    <div>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_important" value="0">
            <input type="checkbox" name="is_important" value="1"
                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                   @checked(old('is_important', $task?->is_important))>
            <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Important</span>
                <p class="text-xs text-gray-400 dark:text-gray-500">Mark if this task moves you towards your goals (Eisenhower Matrix)</p>
            </div>
        </label>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="task_project_id" class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-gray-400">(optional)</span></label>
            <select name="project_id" id="task_project_id"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">No project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(old('project_id', $task?->project_id) == $project->id)>{{ $project->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="task_goal_id" class="block text-sm font-medium text-gray-700 mb-1">Goal <span class="text-gray-400">(optional)</span></label>
            <select name="goal_id" id="task_goal_id"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">No goal</option>
                @foreach($goals as $goal)
                    <option value="{{ $goal->id }}" @selected(old('goal_id', $task?->goal_id) == $goal->id)>{{ $goal->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
