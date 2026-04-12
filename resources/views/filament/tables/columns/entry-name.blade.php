<?php

use App\Models\SportEvent;

/**
 * @var SportEvent $sportEvent
 */
$sportEvent = $getRecord();

?>

<div>
    @if ($sportEvent !== null)
        <div class="ml-2 mb-1">
            <p>
                @if ($sportEvent->cancelled)
                    <span class="line-through text-red-800">{{ Str::limit($sportEvent->name, 40) }}</span>
                @else
                    {{ Str::limit($sportEvent->name, 40) }}
                @endif
                @if ($sportEvent->stages > 0)
                    <span class="ml-1 bg-green-100 text-green-800 text-xs font-medium px-1 py-0.5 rounded dark:bg-gray-700 dark:text-green-400 border border-green-400">
                        {{ $sportEvent->stages }}E
                    </span>
                @endif
                @if (in_array(config('site-config.club.abbr'), $sportEvent->organization))
                    <span class="ml-1 bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-1 py-0.5 rounded dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300">
                        Pořádáme
                    </span>
                @endif
            </p>
            <p class="text-sm text-gray-600">{{ $sportEvent->alt_name }}</p>
            @if (\App\Shared\Helpers\EmptyType::stringNotEmpty($sportEvent->place))
                <p class="text-sm text-gray-600 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ \Illuminate\Support\Str::take($sportEvent->place, 35) }}
                </p>
            @endif
        </div>
    @endif
</div>
