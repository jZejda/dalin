@props([
    'category',
    'size' => 'md',
    'tooltip' => null,
])

@php
    /** @var \App\Enums\PaymentCategory $category */
    [$boxPx, $iconPx] = match ($size) {
        'xs' => [24, 12],
        'sm' => [32, 16],
        'lg' => [48, 24],
        default => [40, 20],
    };

    // Same outline style as x-race-profile-badge (light fill, colored border + content),
    // tinted with the category's Filament color. Full class names keep Tailwind able to see them.
    $colorClasses = match ($category->getColor()) {
        'primary' => 'bg-primary-100 border-primary-500 text-primary-700 dark:bg-primary-900/40 dark:border-primary-400 dark:text-primary-300',
        'info' => 'bg-info-100 border-info-500 text-info-700 dark:bg-info-900/40 dark:border-info-400 dark:text-info-300',
        'warning' => 'bg-warning-100 border-warning-500 text-warning-700 dark:bg-warning-900/40 dark:border-warning-400 dark:text-warning-300',
        'success' => 'bg-success-100 border-success-500 text-success-700 dark:bg-success-900/40 dark:border-success-400 dark:text-success-300',
        'danger' => 'bg-danger-100 border-danger-500 text-danger-700 dark:bg-danger-900/40 dark:border-danger-400 dark:text-danger-300',
        default => 'bg-gray-100 border-gray-400 text-gray-700 dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300',
    };
@endphp

<span
    {{ $attributes->merge(['class' => 'relative inline-block shrink-0']) }}
    style="width:{{ $boxPx }}px;height:{{ $boxPx }}px;"
    @if (filled($tooltip))
        x-tooltip="{ content: {{ \Illuminate\Support\Js::from($tooltip) }}, theme: $store.theme }"
    @endif
>
    <span class="{{ $colorClasses }} flex h-full w-full items-center justify-center overflow-hidden rounded-full border">
        <x-filament::icon
            :icon="$category->getIcon()"
            style="width:{{ $iconPx }}px;height:{{ $iconPx }}px;"
        />
    </span>
</span>
