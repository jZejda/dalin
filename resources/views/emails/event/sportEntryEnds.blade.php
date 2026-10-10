@php
    /** @var list<array{day: string, month: string, name: string, url: string, meta: string, deadline: string}> $events */
    /** @var int $daysBefore */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/event-entry-ends.club.eyebrow')"
    :title="__('mail/event-entry-ends.club.title')"
    :lead="trans_choice('mail/event-entry-ends.club.lead', $daysBefore, ['count' => $daysBefore])"
    :settings-url="$settingsUrl"
>
@foreach ($events as $event)
<x-mail::club.event :day="$event['day']" :month="$event['month']" :name="$event['name']" :url="$event['url']" :meta="$event['meta']" :deadline="__('mail/common.club_layout.deadline', ['date' => $event['deadline']])" />

@endforeach
<x-mail::club.actions :url="$eventsUrl" :label="__('mail/event-entry-ends.club.action')" />
</x-mail::club.message>
