@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 ' . ($padding ? 'p-5' : '')]) }}>
    {{ $slot }}
</div>
