@php
    /** @var list<array{day: string, month: string, name: string, url: string, meta: string, deadline: string}> $events */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/entry-ends-to-pay.club.eyebrow')"
    :title="__('mail/entry-ends-to-pay.club.title')"
    :lead="__('mail/entry-ends-to-pay.club.lead', ['term' => $term])"
>
<x-mail::club.section :title="$termTitle" />

@foreach ($events as $event)
<x-mail::club.event :day="$event['day']" :month="$event['month']" :name="$event['name']" :url="$event['url']" :meta="$event['meta']" :deadline="__('mail/common.club_layout.deadline', ['date' => $event['deadline']])" />

@endforeach
<x-mail::club.fine>{{ __('mail/entry-ends-to-pay.club.fine') }}</x-mail::club.fine>
</x-mail::club.message>
