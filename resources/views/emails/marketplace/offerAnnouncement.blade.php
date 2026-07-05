<x-mail::message>

## Nová nabídka na tržišti

@component('mail::divider')
**{{ $offer->user?->name }}** vystavil{{ $offer->is_club_offer ? ' za oddíl' : '' }} nabídku
**{{ $offer->title }}**.

@if ($offer->description)
{{ $offer->description }}
@endif

Objednávky do **{{ $offer->closes_at->format('j. n. Y H:i') }}**.
@endcomponent

Nabízené produkty:

@foreach ($offer->products as $product)
- **{{ $product->name }}**
@if ($product->isFree())
— zdarma / výměna
@else
— {{ number_format($product->unit_price, 2, ',', ' ') }} Kč/ks
@endif
@unless ($product->hasUnlimitedQty())
({{ $product->qty_available }} ks)
@endunless
@endforeach

<x-mail::button :url="$marketplaceUrl">
Prohlédnout tržiště
</x-mail::button>

</x-mail::message>
