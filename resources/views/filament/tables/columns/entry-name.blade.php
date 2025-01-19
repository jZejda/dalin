<?php

use App\Models\SportEvent;
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Carbon;

/**
 * @var SportEvent $sportEvent
 */
$sportEvent = $getRecord();

?>

<div>
    @if ($sportEvent !== null)
        <div class="ml-2">
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
        </div>
    @endif
</div>
