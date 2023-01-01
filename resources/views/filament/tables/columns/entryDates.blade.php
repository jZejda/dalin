<div>
    <p class="tracking-tight bg-red-50 bg-amber-100">{{ $getRecord()->entry_date_1->format('m.d.Y H:i') }}</p>
    <p class="text-sm tracking-tight text-gray-500">{{ $getRecord()->entry_date_2?->format('m.d.Y H:i' ?? '') }}</p>
    <p class="text-sm tracking-tight text-gray-500">{{ $getRecord()->entry_date_3?->format('m.d.Y H:i') ?? '' }}</p>
</div>
