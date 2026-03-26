@props(['color' => 'gray', 'size' => 'sm'])

@php
$colors = [
    'gray'    => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    'indigo'  => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400',
    'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
    'amber'   => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
    'rose'    => 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-400',
    'blue'    => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
    'purple'  => 'bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-400',
    'pink'    => 'bg-pink-50 text-pink-700 dark:bg-pink-950 dark:text-pink-400',
    'teal'    => 'bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-400',
    'orange'  => 'bg-orange-50 text-orange-700 dark:bg-orange-950 dark:text-orange-400',
    'red'     => 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-400',
];

$sizes = [
    'xs' => 'px-1.5 py-0.5 text-[10px]',
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-sm',
];

$colorClass = $colors[$color] ?? $colors['gray'];
$sizeClass = $sizes[$size] ?? $sizes['sm'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full {$colorClass} {$sizeClass}"]) }}>
    {{ $slot }}
</span>
