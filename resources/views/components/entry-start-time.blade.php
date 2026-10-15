@props([
    // Racer's start (UserEntry::real_start)
    'start' => null,
    // Event zero time (SportEvent::start_time), e.g. "10:30:00"
    'zeroTime' => null,
])

@php
    $time = $start?->format('H:i');
    $minute = \App\Shared\Helpers\StartOffset::minutesFromZero($zeroTime, $time);
@endphp

@if ($time !== null && $minute !== null)
    <div {{ $attributes->merge(['class' => 'flex flex-col leading-tight']) }}
         title="{{ __('filament/common.start_offset_tooltip', ['zero' => substr($zeroTime, 0, 5)]) }}">
        <span class="font-mono text-lg font-semibold text-gray-950 dark:text-white">{{ $minute }}</span>
        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $time }}</span>
    </div>
@elseif ($time !== null)
    <span {{ $attributes }}>{{ $time }}</span>
@endif
