@props([
    'initials' => '',
    'color' => null,
    'avatarUrl' => null,
    'size' => 'md',
])

@php
    [$boxPx, $textPx] = match ($size) {
        'xs' => [24, 10],
        'sm' => [32, 12],
        'lg' => [48, 16],
        default => [40, 14],
    };

    $boxStyle = "width:{$boxPx}px;height:{$boxPx}px;";

    $shade = $color instanceof \App\Enums\BadgeColor ? $color->shade() : $color;
@endphp

@if ($avatarUrl)
    <img
        src="{{ $avatarUrl }}"
        alt="{{ $initials }}"
        style="{{ $boxStyle }}"
        {{ $attributes->merge(['class' => 'inline-block shrink-0 rounded-full object-cover']) }}
    />
@else
    <span
        style="{{ $boxStyle }} background-color: {{ $shade }}; font-size: {{ $textPx }}px; line-height: 1;"
        {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full font-semibold text-white']) }}
    >{{ $initials }}</span>
@endif
