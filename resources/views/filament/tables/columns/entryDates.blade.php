<?php
    use App\Shared\Helpers\AppHelper;
    use Illuminate\Support\Carbon;
?>

<div>
    <p class="
            @if (Carbon::now() > $getRecord()->entry_date_1)
                text-danger-600
            @endif
    ">{{ $getRecord()->entry_date_1?->format(AppHelper::DATE_TIME_FORMAT ?? '') }}</p>

    <p class="text-sm tracking-tight
            @if (Carbon::now() > $getRecord()->entry_date_2)
                text-danger-600
            @else
                text-gray-500
            @endif
    ">{{ $getRecord()->entry_date_2?->format(AppHelper::DATE_TIME_FORMAT ?? '') }}</p>
    <p class="text-sm tracking-tight
            @if (Carbon::now() > $getRecord()->entry_date_2)
                text-danger-600
            @else
                text-gray-500
            @endif
    ">{{ $getRecord()->entry_date_3?->format(AppHelper::DATE_TIME_FORMAT) ?? '' }}</p>
</div>
