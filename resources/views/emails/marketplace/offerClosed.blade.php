<x-mail::message>

## {{ __('marketplace.mail.offer_closed_heading') }}

@component('mail::divider')
{{ __('marketplace.mail.offer_closed_intro', ['title' => $offer->title, 'author' => $offer->user?->name, 'date' => $offer->closed_at?->format('j. n. Y H:i')]) }}
@endcomponent

@if ($isAuthor)
{{ __('marketplace.mail.offer_closed_author_note') }}
@endif

@if ($recipientOrders->isNotEmpty())
{{ __('marketplace.mail.offer_closed_orders_heading') }}

@foreach ($recipientOrders as $order)
- **{{ $order->marketProduct?->name }}** — {{ __('marketplace.mail.qty_suffix', ['qty' => $order->qty]) }}
@if ($order->unit_price > 0)
{{ __('marketplace.mail.offer_closed_unit_price', ['price' => number_format($order->unit_price, 2, ',', ' ')]) }}
= {{ __('marketplace.mail.offer_closed_total_price', ['total' => number_format($order->totalAmount(), 2, ',', ' ')]) }}
({{ $order->marketProduct?->payment_method->getLabel() }})
@else
({{ __('marketplace.mail.free_product_label') }})
@endif
@endforeach

{{ __('marketplace.mail.offer_closed_footer_credit') }}
{{ __('marketplace.mail.offer_closed_footer_direct') }}
@endif

</x-mail::message>
