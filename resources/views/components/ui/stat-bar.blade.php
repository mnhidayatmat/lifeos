@props(['stat', 'value' => 0, 'label' => null])

@php
$statColors = [
    'discipline' => 'bg-stat-discipline',
    'focus'      => 'bg-stat-focus',
    'knowledge'  => 'bg-stat-knowledge',
    'strength'   => 'bg-stat-strength',
    'wealth'     => 'bg-stat-wealth',
    'creativity' => 'bg-stat-creativity',
    'influence'  => 'bg-stat-influence',
    'wisdom'     => 'bg-stat-wisdom',
];
$color = $statColors[$stat] ?? 'bg-gray-400';
$displayLabel = $label ?? ucfirst($stat);
@endphp

<div class="flex items-center gap-3">
    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-20 truncate">{{ $displayLabel }}</span>
    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
        <div class="{{ $color }} h-2 rounded-full transition-all duration-700 ease-out" style="width: {{ min($value, 100) }}%"></div>
    </div>
    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-10 text-right">{{ $value }}</span>
</div>
