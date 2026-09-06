@php use App\Enums\BadgeColor; @endphp
@props([
    'profile',
    'size' => 'md',
])

@php
    $licence = in_array($profile?->licence_ob, [null, '', '-'], true) ? 'C' : $profile->licence_ob;

    $dotColor = $profile && $profile->user_id === auth()->id() ? BadgeColor::Green : null;
@endphp

@if ($profile)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
        <x-race-profile-badge
            :initials="$profile->initials"
            :gender="$profile->gender"
            :dot-color="$dotColor"
            :size="$size"
        />
        <div class="min-w-0 leading-tight">
            <div
                class="font-bold text-gray-950 dark:text-white truncate">{{ $profile->first_name }} {{ $profile->last_name }}</div>
            <div class="text-sm font-mono text-gray-500 dark:text-gray-400 truncate">{{ $profile->reg_number }}<span class="mx-0.5">·</span>{{ $licence }}</div>
        </div>
    </div>
@endif
