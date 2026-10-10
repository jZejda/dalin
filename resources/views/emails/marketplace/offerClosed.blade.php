@php
    /** @var bool $isAuthor */
    /** @var list<array{name: string, detail: string, amount: string}> $orders */
    $key = $isAuthor ? 'marketplace.mail.club.closed_author' : 'marketplace.mail.club.closed';
@endphp
<x-mail::club.message
    :eyebrow="__($key.'.eyebrow')"
    :title="__($key.'.title')"
    :lead="__($key.'.lead', ['title' => $title, 'author' => $author, 'date' => $closedAt])"
>
@if ($isAuthor)
<x-mail::club.note :label="__('marketplace.mail.club.closed_author.note_label')">{{ __('marketplace.mail.club.closed_author.note') }}</x-mail::club.note>

@endif
@if ($orders !== [])
<x-mail::club.section :title="__('marketplace.mail.club.closed.orders_heading')" />

@foreach ($orders as $order)
<x-mail::club.person :name="$order['name']" :lines="[['text' => $order['detail']]]" :amount="$order['amount']" />

@endforeach
@endif
@if ($payByCredit || $payDirectly)
<x-mail::club.note :label="__('marketplace.mail.club.closed.payment_label')">{{ collect([
    $payByCredit ? __('marketplace.mail.club.closed.payment_credit') : null,
    $payDirectly ? __('marketplace.mail.club.closed.payment_direct') : null,
])->filter()->implode(' ') }}</x-mail::club.note>

@endif
</x-mail::club.message>
