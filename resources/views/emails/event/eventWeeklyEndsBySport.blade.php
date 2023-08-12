<?php

use App\Models\SportEvent;
use Illuminate\Support\Carbon;

/** @var SportEvent[] $sportEventContent */
?>

<x-mail::message>

## Konec přihlášek - 1 termín

Blíží ze konec přihlášek na závody vypasané níže. Závody mají konec přihlášek prvního termínu v rámci příštího týdne
od **{{ Carbon::now()->addDay()->format('d.m.Y') }}** do **{{ Carbon::now()->addDays(8)->format('d.m.Y') }}**.

@component('mail::table')
    | Přihláška do       | Název akce/závodu        |
    | :----------------- |:------------- |
    @foreach ($sportEventContent as $sportEvent)
        | {{ Carbon::parse($sportEvent->entry_date_1)->format('d.m.Y - H:i') }}  | {{$sportEvent->name }} |
    @endforeach
@endcomponent

Mějte se fajn a jezděte na závody - ABM

@component('mail::subcopy')
    Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.
@endcomponent

</x-mail::message>
