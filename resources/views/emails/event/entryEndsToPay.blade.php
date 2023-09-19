<?php

use App\Models\SportEvent;
use Illuminate\Support\Carbon;

/** @var SportEvent[] $sportEvents */
/** @var int $deadline */
?>

<x-mail::message>

## Konec přihlášek závodu

Aktuálně končí **{{$deadline}} termín** přihlášek k závodu vypsaných níže. Prosím o uhrazení startovného přihlášených členů.

@if(!is_null($sportEvents))
@component('mail::divider')
## {{$deadline}} termín přihlášek

Závody u kterých právě končí **{{$deadline}} termín** přihlášek.
@endcomponent

@component('mail::table')
    | Přihláška do       | Název akce/závodu        | ORIS ID
    | :----------------- |:------------- |:------------- |
    @foreach ($sportEvents as $event)
        | {{ Carbon::parse($event->entry_date_1)->format('d.m.Y - H:i') }}  | {{$event->name }} | @if($event->oris_id !== null)[{{$event->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$event->oris_id}})@endif  |
    @endforeach
@endcomponent
@endif

</x-mail::message>
