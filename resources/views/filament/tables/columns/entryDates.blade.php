<?php
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Carbon;
use App\Models\SportEvent;

/**
* @var SportEvent $sportEvent
*/
$sportEvent = $getRecord();
?>
<div>
    @if ($sportEvent->entry_date_1 !== null)
        <p class="
            @if ((Carbon::createFromFormat('Y-m-d H:i:s', $sportEvent->entry_date_1)->subDays(5) <= Carbon::now()) && ( Carbon::now() <= $sportEvent->entry_date_1))
                text-warning-600
            @elseif(Carbon::now() > $sportEvent->entry_date_1)
                text-danger-600
            @endif
        ">{{ $sportEvent->entry_date_1?->format(AppHelper::DATE_TIME_FORMAT ?? '') }}</p>
    @endif

    @if ($getRecord()->entry_date_2 !== null)
        <div class="text-sm tracking-tight
            @if ((Carbon::createFromFormat('Y-m-d H:i:s', $sportEvent->entry_date_2)->subDays(5) <= Carbon::now()) && ( Carbon::now() <= $sportEvent->entry_date_2))
               text-warning-600
            @elseif(Carbon::now() > $sportEvent->entry_date_2)
               text-danger-600
            @endif
        ">
            <div class="flex justify-start">
                <div>{{ $sportEvent->entry_date_2?->format(AppHelper::DATE_TIME_FORMAT ?? '') }}</div>
                @if ($sportEvent->increase_entry_fee_2 !== null)
                <div class="ml-1 bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-1 py-0.5 rounded dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300">+{{$sportEvent->increase_entry_fee_2}}%</div>
                @endif
            </div>
        </div>
    @endif

    @if ($getRecord()->entry_date_3 !== null)
        <div class="text-sm tracking-tight
            @if ((Carbon::createFromFormat('Y-m-d H:i:s', $sportEvent->entry_date_3)->subDays(5) <= Carbon::now()) && ( Carbon::now() <= $sportEvent->entry_date_3))
                text-warning-600
            @elseif(Carbon::now() > $sportEvent->entry_date_3)
                text-danger-600
            @endif
        "><div class="flex justify-start">
                <div>{{ $sportEvent->entry_date_3?->format(AppHelper::DATE_TIME_FORMAT ?? '') }}</div>
                @if ($sportEvent->increase_entry_fee_3 !== null)
                    <div class="ml-1 bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-1 py-0.5 rounded dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300">+{{$sportEvent->increase_entry_fee_3}}%</div>
                @endif
            </div>
        </div>
    @endif
</div>
