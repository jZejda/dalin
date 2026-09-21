<?php
    use App\Models\SportEvent;

    /** @var SportEvent $sportEvent  */
    $sportEvent = $getRecord();
    $hasOffers = $sportEvent->transportOffers->where('active', true)->isNotEmpty();
    $freeSeats = $hasOffers ? $sportEvent->transportFreeSeats() : 0;
?>

<div class="ml-4">
    @if($hasOffers)
        <div class="relative py-2" x-tooltip="{ content: {{ \Illuminate\Support\Js::from(__('sport-event.table.transport')) }}, theme: $store.theme }">
            <div class="t-0 absolute left-3">
                <p @class([
                    'flex h-2 w-2 items-center justify-center rounded-full p-3 text-xs',
                    'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-300' => $freeSeats > 0,
                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => $freeSeats === 0,
                ])>{{ $freeSeats }}</p>
            </div>
            <x-filament::icon :icon="\CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon::CarFront" class="mt-4 h-6 w-6" />
        </div>
    @endif
</div>
