@props([
    'initials' => '',
    'color' => null,
    'avatarUrl' => null,
    'size' => 'md',
    'dotColor' => null,
])

@php
    [$boxPx, $textPx] = match ($size) {
        'xs' => [24, 10],
        'sm' => [32, 12],
        'lg' => [48, 16],
        'xl' => [72, 24],
        default => [40, 14],
    };

    $dotPx = max(8, (int) round($boxPx * 0.3));

    $shade = $color instanceof \App\Enums\BadgeColor ? $color->shade() : $color;
    // Neutral gray-500, used when there is no color and no avatar (e.g. an "N/A" placeholder).
    $shade ??= 'oklch(0.551 0.027 264.364)';

    // Small indicator dot in the top-right corner (e.g. red for a deactivated account).
    // Any fill color can be passed in; null means no dot is shown at all.
    $dotShade = $dotColor instanceof \App\Enums\BadgeColor ? $dotColor->shade() : $dotColor;
@endphp

<span
    {{ $attributes->merge(['class' => 'relative inline-block shrink-0']) }}
    style="width:{{ $boxPx }}px;height:{{ $boxPx }}px;"
>
    @if ($avatarUrl)
        <img
            src="{{ $avatarUrl }}"
            alt="{{ $initials }}"
            class="block h-full w-full rounded-full object-cover"
        />
    @else
        <span
            style="background-color: {{ $shade }}; font-size: {{ $textPx }}px; line-height: 1;"
            class="flex h-full w-full items-center justify-center overflow-hidden rounded-full font-semibold text-white"
        >{{ $initials }}</span>
    @endif

    @if ($dotShade)
        <span
            class="absolute -top-0.5 -right-0.5 rounded-full ring-2 ring-white dark:ring-gray-900"
            style="width:{{ $dotPx }}px;height:{{ $dotPx }}px;background-color:{{ $dotShade }};"
        ></span>
    @endif
</span>
