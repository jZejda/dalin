@props([
    // list<array{category: \App\Enums\PaymentCategory, amount: float, count: int}>
    'rows' => [],
])

@php
    $total = round(array_sum(array_column($rows, 'amount')), 2);

    $money = fn (float $amount): string => \Illuminate\Support\Number::currency($amount, in: 'CZK', locale: app()->getLocale());
    $amountClasses = fn (float $amount): string => $amount < 0
        ? 'text-danger-600 dark:text-danger-400'
        : 'text-success-600 dark:text-success-400';
    $labelClasses = fn (\App\Enums\PaymentCategory $category): string => match ($category->getColor()) {
        'primary' => 'text-primary-700 dark:text-primary-300',
        'info' => 'text-info-700 dark:text-info-300',
        'warning' => 'text-warning-700 dark:text-warning-300',
        'success' => 'text-success-700 dark:text-success-300',
        'danger' => 'text-danger-700 dark:text-danger-300',
        default => 'text-gray-700 dark:text-gray-300',
    };
@endphp

<div class="min-w-56 space-y-1.5 py-1 text-left text-sm">
    <div class="pb-1 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('user-credit.payment_summary.heading') }}</div>

    @foreach ($rows as $row)
        <div class="flex items-center gap-2">
            <x-payment-category-badge :category="$row['category']" size="xs" />
            <span class="font-semibold {{ $labelClasses($row['category']) }}">{{ $row['category']->getLabel() }}</span>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $row['count'] }}×</span>
            <span class="ms-auto ps-4 font-medium tabular-nums whitespace-nowrap {{ $amountClasses($row['amount']) }}">{{ $money($row['amount']) }}</span>
        </div>
    @endforeach

    @if (count($rows) > 1)
        <div class="flex items-center gap-2 border-t border-gray-200 pt-1.5 dark:border-white/10">
            <span class="font-semibold text-gray-950 dark:text-white">{{ __('user-credit.payment_summary.total') }}</span>
            <span class="ms-auto ps-4 font-semibold tabular-nums whitespace-nowrap {{ $amountClasses($total) }}">{{ $money($total) }}</span>
        </div>
    @endif
</div>
