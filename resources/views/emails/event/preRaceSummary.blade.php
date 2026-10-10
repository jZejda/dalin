@php
    /** @var \App\Models\SportEvent $event */
    /** @var list<array{icon: string, label: string, value: string}> $facts */
    /** @var list<array{name: string, detail: ?string, extra: ?string, lines: list<array{icon: string, text: string}>}> $runners */
    $lead = $event->name.(filled($event->alt_name) ? ' – '.$event->alt_name : '');
@endphp
<x-mail::club.message
    :eyebrow="__('mail/pre-race-summary.club.eyebrow', ['date' => $eyebrowDate])"
    :title="__('mail/pre-race-summary.club.title')"
    :lead="__('mail/pre-race-summary.club.lead', ['event' => $lead])"
    :settings-url="$settingsUrl"
>
@if ($facts !== [])
<x-mail::club.facts :items="$facts" />

@endif
@if ($runners !== [])
<x-mail::club.section :title="__('mail/pre-race-summary.club.runners_heading')" />

@foreach ($runners as $runner)
<x-mail::club.person :name="$runner['name']" :detail="$runner['detail']" :extra="$runner['extra']" :lines="$runner['lines']" />

@endforeach
@endif
<x-mail::club.actions :url="$detailUrl" :label="__('mail/pre-race-summary.club.action')" :secondary-url="$orisUrl" :secondary-label="__('mail/pre-race-summary.club.oris_link')" />

<x-mail::club.fine>{{ __('mail/pre-race-summary.club.fine') }}</x-mail::club.fine>

@if (filled($event->event_info))
<x-mail::club.section :title="__('mail/pre-race-summary.body.description_heading')" />

{{ $event->event_info }}

@endif
@if ($event->sportEventLinks->isNotEmpty())
<x-mail::club.section :title="__('mail/pre-race-summary.body.links_heading')" />

@foreach ($event->sportEventLinks as $link)
@if ($link->url())
- [{{ $link->label() }}]({{ $link->url() }})
@else
- {{ $link->label() }}
@endif
@endforeach

@endif
@if ($event->sportEventNews->isNotEmpty())
<x-mail::club.section :title="__('mail/pre-race-summary.body.news_heading')" />

@foreach ($event->sportEventNews as $news)
**{{ $news->date->format('j. n. Y') }}** – {{ $news->text }}

@endforeach
@endif
</x-mail::club.message>
