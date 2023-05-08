<?php
use App\Models\SportEvent;

/**
 * @description  https://erikflowers.github.io/weather-icons/
 * @description  https://openweathermap.org/weather-conditions#Weather-Condition-Codes-2
 * @var SportEvent $sportEvent
 */
$sportEvent = $getRecord();
$iconString = isset($sportEvent->weather['weather'][0]['icon']) ? $sportEvent->weather['weather'][0]['icon'] : '';

?>

<div class="ml-4">
    @if(!is_null($sportEvent->weather))
        <div class="flex flex-row space-x-2 items-center">
            <div>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="mt-2 file: h-10 w-10">
                    @if($iconString === '01d' || $iconString === '01n')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14.828 14.828a4 4 0 1 0 -5.656 -5.656a4 4 0 0 0 5.656 5.656z"></path>
                        <path d="M6.343 17.657l-1.414 1.414"></path>
                        <path d="M6.343 6.343l-1.414 -1.414"></path>
                        <path d="M17.657 6.343l1.414 -1.414"></path>
                        <path d="M17.657 17.657l1.414 1.414"></path>
                        <path d="M4 12h-2"></path>
                        <path d="M12 4v-2"></path>
                        <path d="M20 12h2"></path>
                        <path d="M12 20v2"></path>
                    @elseif($iconString == '02d' || $iconString == '03d' || $iconString == '04d')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M6.657 18c-2.572 0 -4.657 -2.007 -4.657 -4.483c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.913 0 3.464 1.56 3.464 3.486c0 1.927 -1.551 3.487 -3.465 3.487h-11.878"></path>
                    @elseif($iconString === '09d' || $iconString === '10d')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7"></path>
                        <path d="M11 13v2m0 3v2m4 -5v2m0 3v2"></path>
                    @elseif($iconString === '11d')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
                        <path d="M13 14l-2 4l3 0l-2 4"></path>
                    @elseif($iconString === '13d')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7"></path>
                        <path d="M11 15v.01m0 3v.01m0 3v.01m4 -4v.01m0 3v.01"></path>
                    @elseif($iconString === '50d')
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 16a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-12"></path>
                        <path d="M5 20l14 0"></path>
                        @endif
                    </svg>
                </span>
            </div>
            <div>
                <h4 class="text-lg">{{isset($sportEvent->weather['main']['temp']) ? $sportEvent->weather['main']['temp'] : ''}}&deg;C</h4>
                <p class="text-xs text-gray-500">{{isset($sportEvent->weather['weather'][0]['description']) ? $sportEvent->weather['weather'][0]['description'] : ''}}</p>
            </div>
        </div>
    @endif

</div>




