@props([
    'profile',
    'size' => 'md',
])

@php
    $dotColor = $profile && $profile->user_id === auth()->id() ? \App\Enums\BadgeColor::Green : null;
@endphp

@if ($profile)
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }}>
        <x-race-profile-badge
            :initials="$profile->initials"
            :gender="$profile->gender"
            :dot-color="$dotColor"
            :tooltip="$profile->user_race_full_name"
            :size="$size"
        />
        <span class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ $profile->reg_number }}</span>
    </div>
@endif
