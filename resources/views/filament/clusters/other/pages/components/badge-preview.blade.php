@php
    /** @var \App\Enums\BadgeColor|null $color */
@endphp

<div class="flex items-center justify-center py-2">
    <x-user-badge :initials="$initials" :color="$color" size="lg" />
</div>
