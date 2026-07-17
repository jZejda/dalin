<x-mail::message>

## {{ __('marketplace.mail.announcement_heading') }}

@component('mail::divider')
{{ $offer->is_club_offer
    ? __('marketplace.mail.announcement_intro_club', ['user' => $offer->user?->name, 'title' => $offer->title])
    : __('marketplace.mail.announcement_intro', ['user' => $offer->user?->name, 'title' => $offer->title]) }}

@if ($offer->description)
{{ $offer->description }}
@endif

{{ __('marketplace.mail.announcement_orders_until', ['date' => $offer->closes_at->format('j. n. Y H:i')]) }}
@endcomponent

{{ __('marketplace.mail.announcement_products_heading') }}

@foreach ($offer->products as $product)
- **{{ $product->name }}**
@if ($product->isFree())
— {{ __('marketplace.mail.free_product_label') }}
@else
— {{ __('marketplace.mail.announcement_price_per_unit', ['price' => number_format($product->unit_price, 2, ',', ' ')]) }}
@endif
@unless ($product->hasUnlimitedQty())
({{ __('marketplace.mail.qty_suffix', ['qty' => $product->qty_available]) }})
@endunless
@endforeach

<x-mail::button :url="$marketplaceUrl">
{{ __('marketplace.browse_marketplace') }}
</x-mail::button>

</x-mail::message>
