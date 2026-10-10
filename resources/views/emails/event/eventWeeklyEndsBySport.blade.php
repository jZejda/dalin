@php
    /** @var array<string, list<array{day: string, month: string, name: string, url: string, meta: string, deadline: string}>> $terms */
    /** @var int $eventCount */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/event-weekly-ends-by-sport.club.eyebrow', ['from' => $from, 'to' => $to])"
    :title="__('mail/event-weekly-ends-by-sport.club.title')"
    :lead="trans_choice('mail/event-weekly-ends-by-sport.club.lead', $eventCount, ['count' => $eventCount])"
    :settings-url="$settingsUrl"
>
@foreach ($terms as $termTitle => $events)
<x-mail::club.section :title="$termTitle" />

@foreach ($events as $event)
<x-mail::club.event :day="$event['day']" :month="$event['month']" :name="$event['name']" :url="$event['url']" :meta="$event['meta']" :deadline="__('mail/event-weekly-ends-by-sport.club.deadline', ['date' => $event['deadline']])" />

@endforeach
@endforeach
<x-mail::club.actions :url="$eventsUrl" :label="__('mail/event-weekly-ends-by-sport.club.action')" />
</x-mail::club.message>
