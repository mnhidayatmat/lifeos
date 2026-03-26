@props(['icon' => null, 'title', 'description' => null, 'action' => null, 'actionUrl' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-4 text-center']) }}>
    @if($icon)
        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
            <x-icon :name="$icon" class="w-6 h-6 text-gray-400 dark:text-gray-500" />
        </div>
    @endif

    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">{{ $title }}</h3>

    @if($description)
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">{{ $description }}</p>
    @endif

    @if($action)
        <a href="{{ $actionUrl ?? '#' }}" class="mt-4 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
            <x-icon name="plus" class="w-4 h-4" />
            {{ $action }}
        </a>
    @endif
</div>
