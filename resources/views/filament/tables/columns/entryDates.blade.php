@php
use Illuminate\Support\Carbon;
use App\Models\SportEvent;

/** @var SportEvent $sportEvent */
$sportEvent = $getRecord();

$tileClasses = function (Carbon $date): string {
    $now = Carbon::now();
    if ($now > $date) {
        return 'border border-dashed border-gray-200 bg-gray-50 text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-500';
    }
    if ($date->copy()->subDays(5) <= $now) {
        return 'border border-dashed border-orange-300 bg-orange-50 text-orange-700 dark:border-orange-700 dark:bg-orange-950 dark:text-orange-300';
    }
    return 'border border-dashed border-green-300 bg-green-50 text-green-700 dark:border-green-700 dark:bg-green-950 dark:text-green-300';
};
@endphp

<div class="flex flex-nowrap">
    @if ($sportEvent->entry_date_1 !== null)
        <div class="text-xs px-1.5 py-1 m-1 rounded leading-tight {{ $tileClasses($sportEvent->entry_date_1) }}">
{{--            <div class="font-semibold opacity-70">1.</div>--}}
            <div class="font-medium">{{ $sportEvent->entry_date_1->format('d.m.y') }}</div>
            <div>{{ $sportEvent->entry_date_1->format('H:i') }}</div>
        </div>
    @endif

    @if ($sportEvent->entry_date_2 !== null)
        <div class="text-xs px-1.5 py-1 m-1 rounded leading-tight {{ $tileClasses($sportEvent->entry_date_2) }}">
            <div class="font-semibold opacity-70">
{{--                2.--}}
            </div>
            <div class="font-medium">{{ $sportEvent->entry_date_2->format('d.m.y') }}</div>
            <div>
                {{ $sportEvent->entry_date_2->format('H:i') }}
                @if ($sportEvent->increase_entry_fee_2 !== null)
                    <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 px-0.5 rounded">+{{ $sportEvent->increase_entry_fee_2 }}%</span>
                @endif
            </div>
        </div>
    @endif

    @if ($sportEvent->entry_date_3 !== null)
        <div class="text-xs px-1.5 py-1 m-1 rounded leading-tight {{ $tileClasses($sportEvent->entry_date_3) }}">
            <div class="font-semibold opacity-70">
{{--                3.--}}
            </div>
            <div class="font-medium">{{ $sportEvent->entry_date_3->format('d.m.y') }}</div>
            <div>
                {{ $sportEvent->entry_date_3->format('H:i') }}
                @if ($sportEvent->increase_entry_fee_3 !== null)
                    <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 px-0.5 rounded">+{{ $sportEvent->increase_entry_fee_3 }}%</span>
                @endif
            </div>
        </div>
    @endif
</div>
