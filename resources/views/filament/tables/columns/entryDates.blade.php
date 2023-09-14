<?php
    use App\Shared\Helpers\AppHelper;
    use Illuminate\Support\Carbon;
?>

<div>
    @if ($getRecord()->entry_date_1 !== null)
        <p class="
            @if ((Carbon::createFromFormat('Y-m-d H:i:s', $getRecord()->entry_date_1)->subDays(5) <= Carbon::now()) && ( Carbon::now() <= $getRecord()->entry_date_1))
                text-warning-600
            @elseif(Carbon::now() > $getRecord()->entry_date_1)
                text-danger-600
            @endif
        ">{{ $getRecord()->entry_date_1?->format(AppHelper::DATE_TIME_FORMAT ?? '') }}</p>
    @endif

    @if ($getRecord()->entry_date_2 !== null)
        <p class="text-sm tracking-tight
            @if ((Carbon::createFromFormat('Y-m-d H:i:s', $getRecord()->entry_date_2)->subDays(5) <= Carbon::now()) && ( Carbon::now() <= $getRecord()->entry_date_2))
               text-warning-600
            @elseif(Carbon::now() > $getRecord()->entry_date_2)
               text-danger-600
            @endif
        ">{{ $getRecord()->entry_date_2?->format(AppHelper::DATE_TIME_FORMAT ?? '') }}</p>
    @endif

    @if ($getRecord()->entry_date_3 !== null)
        <p class="text-sm tracking-tight
            @if ((Carbon::createFromFormat('Y-m-d H:i:s', $getRecord()->entry_date_3)->subDays(5) <= Carbon::now()) && ( Carbon::now() <= $getRecord()->entry_date_3))
                text-warning-600
            @elseif(Carbon::now() > $getRecord()->entry_date_3)
                text-danger-600
            @endif
        ">{{ $getRecord()->entry_date_3?->format(AppHelper::DATE_TIME_FORMAT) ?? '' }}</p>
    @endif
</div>
