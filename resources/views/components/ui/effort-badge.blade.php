@props(['effort' => 'medium'])

@php
$styles = [
    'small'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
    'medium' => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
    'large'  => 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-400',
];
$labels = [
    'small'  => 'S',
    'medium' => 'M',
    'large'  => 'L',
];
$xpValues = [
    'small'  => '5 XP',
    'medium' => '15 XP',
    'large'  => '30 XP',
];
$style = $styles[$effort] ?? $styles['medium'];
$label = $labels[$effort] ?? 'M';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center w-6 h-6 rounded text-[10px] font-bold {$style}", 'title' => $xpValues[$effort] ?? '']) }}>
    {{ $label }}
</span>
