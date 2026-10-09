@props([
    'category',
    'description' => null,
    'size' => 'md',
])

@php
    /** @var \App\Enums\PaymentCategory $category */
    $labelClasses = match ($category->getColor()) {
        'primary' => 'text-primary-700 dark:text-primary-300',
        'info' => 'text-info-700 dark:text-info-300',
        'warning' => 'text-warning-700 dark:text-warning-300',
        'success' => 'text-success-700 dark:text-success-300',
        'danger' => 'text-danger-700 dark:text-danger-300',
        default => 'text-gray-700 dark:text-gray-300',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <x-payment-category-badge :category="$category" :size="$size" />
    <div class="min-w-0 leading-tight">
        <div class="font-semibold truncate {{ $labelClasses }}">{{ $category->getLabel() }}</div>
        @if (filled($description))
            <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $description }}</div>
        @endif
    </div>
</div>
