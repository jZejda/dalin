@php
use Carbon\Carbon;

/** @var Carbon $date */
/** @var int $number */
/** @var string|null $fee */

$now = Carbon::now();
$approachingThreshold = $date->copy()->subDays(5);

if ($now > $date) {
    $status = 'past';
} elseif ($now >= $approachingThreshold) {
    $status = 'approaching';
} else {
    $status = 'future';
}

$tileClasses = match($status) {
    'future'     => 'border-dashed border border-green-300 bg-green-50 text-green-700 dark:border-green-700 dark:bg-green-950 dark:text-green-300',
    'approaching' => 'border-dashed border border-orange-300 bg-orange-50 text-orange-700 dark:border-orange-700 dark:bg-orange-950 dark:text-orange-300',
    'past'       => 'border border-gray-200 bg-gray-50 text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-500',
};

$labelClasses = match($status) {
    'future'     => 'text-green-500 dark:text-green-500',
    'approaching' => 'text-orange-500 dark:text-orange-400',
    'past'       => 'text-gray-400 dark:text-gray-600',
};
@endphp

<div class="px-3 py-2 m-0.5 rounded-md transition-colors {{ $tileClasses }}" title="{{ $date->format('d.m.Y H:i') }}">
    <div class="text-xs font-semibold {{ $labelClasses }}">
        {{ $number }}.
    </div>
    <div class="text-sm font-bold mt-0.5">
        {{ $date->format('d.m.y') }}
    </div>
    <div class="text-xs">
        {{ $date->format('H:i') }}
    </div>
    @if (!empty($fee))
        <div class="mt-1">
            <span class="text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 px-1 py-0.5 rounded">
                +{{ $fee }}%
            </span>
        </div>
    @endif
</div>
