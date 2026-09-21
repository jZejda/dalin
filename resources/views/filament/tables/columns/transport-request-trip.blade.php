<?php

/** @var \App\Models\TransportRequest $record */
$record = $getRecord();
$offer = $record->transportOffer;

// Trip parameters shown on one line, separated by a dot.
$parts = [
    ['text' => $record->direction->label(), 'strong' => true],
    ['text' => __('transport.seats').': '.$record->seats],
];

if ($offer?->vehicle) {
    $parts[] = ['text' => $offer->vehicle->name, 'icon' => \CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon::Car];
}

if ($showDeparture && filled($offer?->departure_place)) {
    $parts[] = ['text' => __('transport.departure_place').': '.$offer->departure_place];
}
?>

<div class="px-3 py-2 text-sm">
    <div class="text-gray-700 dark:text-gray-300" style="display:flex;flex-wrap:wrap;align-items:center;column-gap:0.5rem;row-gap:0.25rem;">
        @foreach ($parts as $part)
            <span
                class="{{ ($part['strong'] ?? false) ? 'font-medium text-gray-950 dark:text-white' : '' }}"
                style="display:inline-flex;align-items:center;gap:0.25rem;"
            >
                @if (isset($part['icon']))
                    <x-filament::icon :icon="$part['icon']" class="h-4 w-4 text-gray-400" />
                @endif
                {{ $part['text'] }}
            </span>
            @if (! $loop->last)
                <span class="text-gray-400 dark:text-gray-500" aria-hidden="true">·</span>
            @endif
        @endforeach
    </div>
    @if (filled($record->note))
        <div class="italic text-gray-600 dark:text-gray-400" style="margin-top:0.25rem;">{{ __('transport.note') }}: {{ $record->note }}</div>
    @endif
</div>
