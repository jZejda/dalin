@props([
    'initials' => '',
    'color' => null,
    'avatarUrl' => null,
    'size' => 'md',
    'inactive' => false,
])

@php
    [$boxPx, $textPx] = match ($size) {
        'xs' => [24, 10],
        'sm' => [32, 12],
        'lg' => [48, 16],
        default => [40, 14],
    };

    $dotPx = max(8, (int) round($boxPx * 0.3));

    $shade = $color instanceof \App\Enums\BadgeColor ? $color->shade() : $color;
    // Neutral gray-500, used when there is no color and no avatar (e.g. an "N/A" placeholder).
    $shade ??= 'oklch(0.551 0.027 264.364)';
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

    @if ($inactive)
        <span
            class="absolute -bottom-0.5 -right-0.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900"
            style="width:{{ $dotPx }}px;height:{{ $dotPx }}px;"
        ></span>
    @endif
</span>
