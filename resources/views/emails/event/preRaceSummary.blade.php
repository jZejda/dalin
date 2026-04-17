@php
    use App\Services\OrisApiService;
    use Illuminate\Support\Carbon;
@endphp

<x-mail::message>

## Souhrn před závodem: {{ $event->name }}

---

### Informace o akci

@component('mail::table')
| | |
|:---|:---|
| **Datum** | {{ $event->date->format('d.m.Y') }}@if($event->date_end && $event->date_end->ne($event->date)) – {{ $event->date_end->format('d.m.Y') }} ({{ $event->date->diffInDays($event->date_end) + 1 }} {{ $event->date->diffInDays($event->date_end) + 1 === 1 ? 'den' : 'dny' }})@endif |
| **Start první etapy** | {{ $event->start_time ?? '–' }} |
@if($event->oris_id)
| **ORIS** | [Závod {{ $event->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}) |
@endif
@endcomponent

---

### Závodní profily

@component('mail::table')
| Reg. číslo | Jméno | Kategorie | Start | +min | Délka | Kontroly | Převýšení |
|:---|:---|:---|:---|:---|:---|:---|:---|
@foreach($entries as $entry)
@php
    $profile = $entry->userRaceProfile;
    $startRaw = $entry->requested_start ?? ($entry->real_start?->format('H:i:s'));
    $startDisplay = $startRaw ? \Illuminate\Support\Str::substr($startRaw, 0, 5) : null;
    $relativeMinutes = null;
    if ($startRaw !== null && $event->start_time !== null) {
        try {
            $fmt = strlen(trim($startRaw)) === 5 ? 'H:i' : 'H:i:s';
            $fmtEvent = strlen(trim($event->start_time)) === 5 ? 'H:i' : 'H:i:s';
            $entryCarbon = Carbon::createFromFormat($fmt, trim($startRaw));
            $eventCarbon = Carbon::createFromFormat($fmtEvent, trim($event->start_time));
            $relativeMinutes = (int) $eventCarbon->diffInMinutes($entryCarbon, false);
        } catch (\Exception) {}
    }
    $sportClass = $event->sportClasses->firstWhere('class_definition_id', $entry->class_definition_id);
@endphp
| {{ $profile?->reg_number ?? '–' }} | {{ $profile?->first_name }} {{ $profile?->last_name }} | {{ $entry->class_name ?? '–' }} | {{ $startDisplay ?? '–' }} | {{ $relativeMinutes !== null ? ($relativeMinutes >= 0 ? '+'.$relativeMinutes : $relativeMinutes) : '–' }} | {{ $sportClass?->distance ? $sportClass->distance.' km' : '–' }} | {{ $sportClass?->controls ?? '–' }} | {{ $sportClass?->climbing ? $sportClass->climbing.' m' : '–' }} |
@endforeach
@endcomponent

@if($event->event_info)

---

### Popis akce

{{ $event->event_info }}
@endif

@if($event->sportEventLinks->isNotEmpty())

---

### Linky

@component('mail::table')
| Název | Odkaz |
|:---|:---|
@foreach($event->sportEventLinks as $link)
| {{ $link->name_cz ?? $link->name_en ?? '–' }} | @if($link->source_url)[{{ $link->name_cz ?? $link->source_url }}]({{ $link->source_url }})@else–@endif |
@endforeach
@endcomponent
@endif

@if($event->sportEventNews->isNotEmpty())

---

### Novinky

@foreach($event->sportEventNews as $news)
**{{ $news->date->format('d.m.Y') }}**

{{ $news->text }}

@if(!$loop->last)
---
@endif
@endforeach
@endif

</x-mail::message>
