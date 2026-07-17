@php
    use App\Services\OrisApiService;

    $days = $event->date_end && $event->date_end->ne($event->date) ? $event->date->diffInDays($event->date_end) + 1 : null;
@endphp

<x-mail::message>

## {{ __('mail/pre-race-summary.body.heading', ['event' => $event->name]) }}

{{ $event->alt_name }}

@component('mail::divider')
### {{ __('mail/pre-race-summary.body.info_heading') }}
@endcomponent

@component('mail::table')
| | |
|:---|:---|
| **{{ __('mail/pre-race-summary.body.date_label') }}** | {{ $event->date->format('d.m.Y') }}@if($days !== null) – {{ $event->date_end->format('d.m.Y') }} ({{ trans_choice('mail/pre-race-summary.body.days_count', $days, ['count' => $days]) }})@endif |
| **{{ __('mail/pre-race-summary.body.start_time_label') }}** | {{ $event->start_time ?? '–' }} |
@if($event->oris_id)
| **{{ __('mail/pre-race-summary.body.oris_label') }}** | [{{ __('mail/pre-race-summary.body.oris_link_text', ['id' => $event->oris_id]) }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}) |
@endif
@endcomponent

---

### {{ __('mail/pre-race-summary.body.profiles_heading') }}

@component('mail::table')
| {{ __('mail/pre-race-summary.body.reg_number_label') }} | {{ __('mail/pre-race-summary.body.name_label') }} | {{ __('mail/pre-race-summary.body.category_label') }} | {{ __('mail/pre-race-summary.body.plus_min_label') }} | {{ __('mail/pre-race-summary.body.distance_label') }} | {{ __('mail/pre-race-summary.body.controls_label') }} | {{ __('mail/pre-race-summary.body.climbing_label') }} |
|:---|:---|:---|:---|:---|:---|:---|
@foreach($entries as $entry)
@php
    $profile = $entry->userRaceProfile;
    $startRaw = $entry->requested_start ?? ($entry->real_start?->format('H:i:s'));
    $startDisplay = $startRaw ? \Illuminate\Support\Str::substr($startRaw, 0, 5) : null;
    $relativeMinutes = null;
    $parseTimeToMinutes = static function (string $time): ?int {
        $parts = explode(':', trim($time));
        if (count($parts) < 2) {
            return null;
        }
        return (int) $parts[0] * 60 + (int) $parts[1];
    };
    if ($startRaw !== null && $event->start_time !== null) {
        $entryMinutes = $parseTimeToMinutes($startRaw);
        $eventMinutes = $parseTimeToMinutes($event->start_time);
        if ($entryMinutes !== null && $eventMinutes !== null) {
            $relativeMinutes = $entryMinutes - $eventMinutes;
        }
    }
    $sportClass = $event->sportClasses->firstWhere('class_definition_id', $entry->class_definition_id);
@endphp
| {{ $profile?->reg_number ?? '–' }} | {{ $profile?->first_name }} {{ $profile?->last_name }} | {{ $entry->class_name ?? '–' }} | {{ $relativeMinutes !== null ? ($relativeMinutes >= 0 ? '+'.$relativeMinutes : $relativeMinutes) : '–' }} | {{ $sportClass?->distance ? $sportClass->distance.' km' : '–' }} | {{ $sportClass?->controls ?? '–' }} | {{ $sportClass?->climbing ? $sportClass->climbing.' m' : '–' }} |
@endforeach
@endcomponent

@if($event->event_info)

---

### {{ __('mail/pre-race-summary.body.description_heading') }}

{{ $event->event_info }}
@endif

@if($event->sportEventLinks->isNotEmpty())

---

### {{ __('mail/pre-race-summary.body.links_heading') }}

@component('mail::table')
| {{ __('mail/pre-race-summary.body.link_name_label') }} | {{ __('mail/pre-race-summary.body.link_url_label') }} |
|:---|:---|
@foreach($event->sportEventLinks as $link)
| {{ $link->name_cz ?? $link->description_cz ?? '–' }} | @if($link->source_url)[{{ $link->name_cz ?? $link->source_url }}]({{ $link->source_url }})@else–@endif |
@endforeach
@endcomponent
@endif

@if($event->sportEventNews->isNotEmpty())

---

### {{ __('mail/pre-race-summary.body.news_heading') }}

@foreach($event->sportEventNews as $news)
**{{ $news->date->format('d.m.Y') }}**

{{ $news->text }}

@if(!$loop->last)
---
@endif
@endforeach
@endif

</x-mail::message>
