@props([
    'user',
    'size' => 'md',
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <x-user-badge
        :initials="$user->initials"
        :color="$user->badge_color"
        :avatar-url="$user->avatar_url"
        :size="$size"
    />
    <div class="min-w-0 leading-tight">
        <div class="font-semibold text-gray-950 dark:text-white truncate">{{ $user->name }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</div>
    </div>
</div>
