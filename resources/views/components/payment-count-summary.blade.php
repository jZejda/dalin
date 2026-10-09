@props([
    'count' => 0,
    // list<array{category: \App\Enums\PaymentCategory, amount: float, count: int}>
    'summary' => [],
])

@php
    $popover = $summary === []
        ? null
        : view('components.payment-category-summary', ['rows' => $summary])->render();
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex'])->class(['cursor-help' => $popover !== null]) }}
    @if ($popover !== null)
        x-tooltip.html.interactive="{ content: {{ \Illuminate\Support\Js::from($popover) }}, theme: $store.theme }"
    @endif
>
    <x-filament::badge :color="$count > 0 ? 'primary' : 'gray'">
        {{ $count }}
    </x-filament::badge>
</span>
