@props(['variant' => 'primary', 'href' => null, 'disabled' => false])
@php
    $variantClass = match ($variant) {
        'secondary' => 'terrain-button-secondary',
        'quiet' => 'terrain-button-quiet',
        default => 'terrain-button-primary',
    };
@endphp
@if ($href !== null && !$disabled)
    <a href="{{ $href }}" {{ $attributes->class(['terrain-button', $variantClass]) }}>{{ $slot }}</a>
@else
    <button @disabled($disabled) {{ $attributes->merge(['type' => 'button'])->class(['terrain-button', $variantClass]) }}>{{ $slot }}</button>
@endif
