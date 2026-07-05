<x-mail::message>

## Nabídka na tržišti byla ukončena

@component('mail::divider')
Nabídka **{{ $offer->title }}** od **{{ $offer->user?->name }}** byla ukončena
{{ $offer->closed_at?->format('j. n. Y H:i') }}.
@endcomponent

@if ($isAuthor)
Objednávky členů najdeš v aplikaci na stránce Moje nabídky. Po realizaci nákupu
můžeš nabídku rozúčtovat podle objednaných kusů.
@endif

@if ($recipientOrders->isNotEmpty())
Tvoje objednávky v této nabídce:

@foreach ($recipientOrders as $order)
- **{{ $order->marketProduct?->name }}** — {{ $order->qty }} ks
@if ($order->unit_price > 0)
× {{ number_format($order->unit_price, 2, ',', ' ') }} Kč
= **{{ number_format($order->totalAmount(), 2, ',', ' ') }} Kč**
({{ $order->marketProduct?->payment_method->getLabel() }})
@else
(zdarma / výměna)
@endif
@endforeach

Položky se stržením z konta ti budou naúčtovány při rozúčtování nabídky.
Přímé platby proběhnou po domluvě se zadavatelem.
@endif

</x-mail::message>
