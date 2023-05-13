<x-mail::message>

## Notifikace k zavodu {{ $sportEvent->name }}

@if(!is_null($sportEvent->alt_name))
{{ $sportEvent->alt_name }}
@endif

{{ $content }}

@component('mail::subcopy')
    Zpráva zaslaná přímo správcem závodu/události přihlášeným závodníkům k závodu.
@endcomponent
</x-mail::message>

