@props(['value' => 0, 'max' => 100, 'color' => 'indigo', 'size' => 'md', 'showLabel' => false])

@php
$percentage = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;

$barColors = [
    'indigo'  => 'bg-indigo-500',
    'emerald' => 'bg-emerald-500',
    'amber'   => 'bg-amber-500',
    'rose'    => 'bg-rose-500',
    'blue'    => 'bg-blue-500',
    'purple'  => 'bg-purple-500',
    'teal'    => 'bg-teal-500',
];

$heights = [
    'xs' => 'h-1',
    'sm' => 'h-1.5',
    'md' => 'h-2',
    'lg' => 'h-3',
];

$barColor = $barColors[$color] ?? $barColors['indigo'];
$height = $heights[$size] ?? $heights['md'];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($showLabel)
        <div class="flex justify-between items-center mb-1">
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $slot }}</span>
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $percentage }}%</span>
        </div>
    @endif
    <div class="w-full {{ $height }} bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
        <div class="{{ $barColor }} {{ $height }} rounded-full transition-all duration-500 ease-out" style="width: {{ $percentage }}%"></div>
    </div>
</div>
