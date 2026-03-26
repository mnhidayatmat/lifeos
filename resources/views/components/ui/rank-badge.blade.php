@props(['rank' => 'initiate', 'size' => 'sm'])

@php
$rankStyles = [
    'initiate'   => 'bg-rank-initiate/10 text-rank-initiate border-rank-initiate/20',
    'apprentice' => 'bg-rank-apprentice/10 text-rank-apprentice border-rank-apprentice/20',
    'specialist' => 'bg-rank-specialist/10 text-rank-specialist border-rank-specialist/20',
    'expert'     => 'bg-rank-expert/10 text-rank-expert border-rank-expert/20',
    'master'     => 'bg-rank-master/10 text-rank-master border-rank-master/20',
    'legend'     => 'bg-rank-legend/10 text-rank-legend border-rank-legend/20',
];

$sizes = [
    'xs' => 'px-1.5 py-0.5 text-[10px]',
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
];

$style = $rankStyles[$rank] ?? $rankStyles['initiate'];
$sizeClass = $sizes[$size] ?? $sizes['sm'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full border {$style} {$sizeClass} uppercase tracking-wide"]) }}>
    {{ ucfirst($rank) }}
</span>
