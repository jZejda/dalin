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

@if ($count >= 1)
<span
    {{ $attributes->merge(['class' => 'inline-flex flex-col items-center leading-tight'])->class(['cursor-help' => $popover !== null]) }}
    @if ($popover !== null)
        x-tooltip.html.interactive="{ content: {{ \Illuminate\Support\Js::from($popover) }}, theme: $store.theme }"
    @endif
>
    <span class="text-xl font-semibold text-gray-950 dark:text-white">{{ $count }}</span>
    <span class="text-xs text-gray-400 dark:text-gray-500">{{ trans_choice('sport-event.payment_list.payments_word', $count) }}</span>
</span>
@endif
