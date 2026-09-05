@php
    /** @var \App\Enums\BadgeColor|null $color */
@endphp

<div class="flex flex-col items-center gap-2 py-2 text-center">
    <x-user-badge :initials="$initials" :color="$color" size="xl" />

    <div class="leading-tight">
        <div class="text-lg font-semibold text-gray-950 dark:text-white">{{ $name }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $email }}</div>
    </div>
</div>
