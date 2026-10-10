@php
    /** @var list<array{name: string, detail: string, price: string}> $products */
@endphp
<x-mail::club.message
    :eyebrow="__('marketplace.mail.club.announcement.eyebrow')"
    :title="__($isClubOffer ? 'marketplace.mail.club.announcement.title_club' : 'marketplace.mail.club.announcement.title')"
    :lead="__($isClubOffer ? 'marketplace.mail.club.announcement.lead_club' : 'marketplace.mail.club.announcement.lead', ['author' => $author, 'title' => $title])"
>
@if (filled($description))
<x-mail::club.note :label="__('marketplace.mail.club.announcement.description_label')">{{ $description }}</x-mail::club.note>

@endif
<x-mail::club.pill :label="__('marketplace.mail.club.announcement.orders_until', ['date' => $closesAt])" />

<x-mail::club.section :title="__('marketplace.mail.club.announcement.products_heading')" />

@foreach ($products as $product)
<x-mail::club.person :name="$product['name']" :lines="[['text' => $product['detail']]]" :amount="$product['price']" />

@endforeach
<x-mail::club.actions :url="$marketplaceUrl" :label="__('marketplace.mail.club.announcement.action')" />
</x-mail::club.message>
