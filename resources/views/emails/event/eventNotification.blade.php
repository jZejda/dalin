<x-mail::message>

## Notifikace k zavodu

@component('mail::divider')
Zpráva k závodu **{{ $sportEvent->name }}** @if(!is_null($sportEvent->alt_name)) | {{ $sportEvent->alt_name }} @endif odeslána na všechny aktuálně přihlášené závodníky.

- Datum: **{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sportEvent->date)->format('d.h.Y') ?? ''}}**
- Místo: **{{ $sportEvent->place ?? ''}}**
@endcomponent

{{ $content }}

</x-mail::message>

