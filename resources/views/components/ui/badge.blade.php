@props(['tone' => 'neutral'])
@php
    $colors = match ($tone) {
        'success' => 'bg-terrain-success-soft text-terrain-success',
        'danger' => 'bg-terrain-danger-soft text-terrain-danger',
        'warning' => 'bg-terrain-warning-soft text-terrain-warning',
        default => 'bg-terrain-muted text-terrain-ink',
    };
@endphp
<span {{ $attributes->class(['inline-flex items-center rounded-terrain-control px-2.5 py-1 text-xs font-semibold', $colors]) }}>{{ $slot }}</span>
