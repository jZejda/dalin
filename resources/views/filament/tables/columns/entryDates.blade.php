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

$feeClasses = function (Carbon $date): string {
    $now = Carbon::now();
    if ($now > $date) {
        return 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500';
    }
    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
};
@endphp

<div class="flex flex-col gap-0.5">
    @if ($sportEvent->entry_date_1 !== null)
        <div class="text-xs px-2 py-0.5 rounded leading-tight w-26 flex items-center gap-1 {{ $tileClasses($sportEvent->entry_date_1) }}">
            <span class="font-medium">{{ $sportEvent->entry_date_1->format('d.m.y') }}</span>
            <span class="opacity-80">{{ $sportEvent->entry_date_1->format('H:i') }}</span>
        </div>
    @endif

    @if ($sportEvent->entry_date_2 !== null)
        <div class="text-xs px-2 py-0.5 rounded leading-tight w-26 flex items-center gap-1 {{ $tileClasses($sportEvent->entry_date_2) }}">
            <span class="font-medium">{{ $sportEvent->entry_date_2->format('d.m.y') }}</span>
            <span class="opacity-80">{{ $sportEvent->entry_date_2->format('H:i') }}</span>
            @if ($sportEvent->increase_entry_fee_2 !== null)
                <span class="ml-3 px-0.5 rounded {{ $feeClasses($sportEvent->entry_date_2) }}">+{{ $sportEvent->increase_entry_fee_2 }}%</span>
            @endif
        </div>
    @endif
    @if ($sportEvent->entry_date_3 !== null)
        <div class="text-xs px-2 py-0.5 rounded leading-tight w-26 flex items-center gap-1 {{ $tileClasses($sportEvent->entry_date_3) }}">
            <span class="font-medium">{{ $sportEvent->entry_date_3->format('d.m.y') }}</span>
            <span class="opacity-80">{{ $sportEvent->entry_date_3->format('H:i') }}</span>
            @if ($sportEvent->increase_entry_fee_3 !== null)
                <span class="ml-2.5 px-0.5 rounded {{ $feeClasses($sportEvent->entry_date_3) }}">+{{ $sportEvent->increase_entry_fee_3 }}%</span>
            @endif
        </div>
    @endif
</div>
