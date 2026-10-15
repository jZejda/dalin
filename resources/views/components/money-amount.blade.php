@props([
    // Amount in CZK; green when >= 0, red when negative
    'amount' => 0,
])

@php
    $amount = (float) $amount;
    $color = $amount >= 0 ? 'text-success-600' : 'text-danger-600';
    $formatted = \Illuminate\Support\Number::format($amount, precision: 2, locale: 'cs') ?: number_format($amount, 2, ',', ' ');
@endphp

<span class="{{ $color }}">{{ $formatted }}</span><span class="text-gray-400 dark:text-gray-500 text-sm ml-1">Kč</span>
