@props([
    'initials' => '',
    'gender' => null,
    'size' => 'md',
    'dotColor' => null,
    'tooltip' => null,
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

    // Outline style (light fill, dark border + text) distinguishes a race profile badge
    // from the solid x-user-badge used for actual user accounts.
    $colorClasses = match ($gender) {
        'H' => 'bg-blue-100 border-blue-500 text-blue-700 dark:bg-blue-900/40 dark:border-blue-400 dark:text-blue-300',
        'D' => 'bg-purple-100 border-purple-500 text-purple-700 dark:bg-purple-900/40 dark:border-purple-400 dark:text-purple-300',
        default => 'bg-gray-100 border-gray-400 text-gray-700 dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300',
    };

    $dotShade = $dotColor instanceof \App\Enums\BadgeColor ? $dotColor->shade() : $dotColor;
@endphp

<span
    {{ $attributes->merge(['class' => 'relative inline-block shrink-0']) }}
    style="width:{{ $boxPx }}px;height:{{ $boxPx }}px;"
    @if (filled($tooltip))
        x-tooltip="{ content: {{ \Illuminate\Support\Js::from($tooltip) }}, theme: $store.theme }"
    @endif
>
    <span
        style="font-size: {{ $textPx }}px; line-height: 1;"
        class="{{ $colorClasses }} flex h-full w-full items-center justify-center overflow-hidden rounded-full border font-semibold"
    >{{ $initials }}</span>

    @if ($dotShade)
        <span
            class="absolute -top-0.5 -right-0.5 rounded-full ring-2 ring-white dark:ring-gray-900"
            style="width:{{ $dotPx }}px;height:{{ $dotPx }}px;background-color:{{ $dotShade }};"
        ></span>
    @endif
</span>
