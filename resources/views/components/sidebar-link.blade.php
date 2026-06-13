@props(['active' => false, 'icon' => null, 'href' => '#'])

@php
$classes = $active
    ? 'flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-400'
    : 'flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200 transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <x-icon :name="$icon" class="w-5 h-5 shrink-0" />
    @endif
    <span>{{ $slot }}</span>
</a>
