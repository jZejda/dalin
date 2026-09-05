<?php
use App\Models\SportEvent;

/**
 * @var SportEvent $sportEvent
 */
$sportEvent = $getRecord();
$weatherId = isset($sportEvent->weather['weather'][0]['id']) ? (int) $sportEvent->weather['weather'][0]['id'] : null;

?>

<div class="ml-4">
    @if(!is_null($sportEvent->weather))
        <div class="flex flex-row space-x-2 items-center">
            <div>
                <x-weather-icon :weather-id="$weatherId" class="mt-2 h-10 w-10" />
            </div>
            <div>
                <h4 class="text-lg">{{isset($sportEvent->weather['main']['temp']) ? round($sportEvent->weather['main']['temp'], 1) : ''}}&deg;C</h4>
                <p class="text-xs text-gray-500">{{isset($sportEvent->weather['weather'][0]['description']) ? $sportEvent->weather['weather'][0]['description'] : ''}}</p>
            </div>
        </div>
    @endif

</div>




