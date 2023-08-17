<?php

use Illuminate\Support\Collection;
use App\Models\SportEvent;
use Illuminate\Support\Carbon;

/** @var SportEvent[]|Collection $eventFirstDateEnd */
/** @var SportEvent[]|Collection $eventSecondDateEnd */
/** @var SportEvent[]|Collection $eventThirdDateEnd */
?>

<x-mail::message>

## Konec přihlášek

Blíží ze konec přihlášek na závody vypasané níže. Závody mají konec přihlášek prvního termínu v rámci příštího týdne
od **{{ Carbon::now()->addDay()->format('d.m.Y') }}** do **{{ Carbon::now()->addDays(8)->format('d.m.Y') }}**.

@component('mail::divider')
## 1 termín přihlášek

Závody u kterých končí *první termín* přihlášek.
@endcomponent

@component('mail::table')
    | Přihláška do       | Název akce/závodu        | ORIS ID
    | :----------------- |:------------- |:------------- |
    @foreach ($eventFirstDateEnd as $firstDate)
        | {{ Carbon::parse($firstDate->entry_date_1)->format('d.m.Y - H:i') }}  | {{$firstDate->name }} | @if($firstDate->oris_id !== null)[{{$firstDate->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$firstDate->oris_id}})@endif  |
    @endforeach
@endcomponent

@if(!is_null($eventSecondDateEnd))

@component('mail::divider')
## 2 termín přihlášek

Závody u kterých končí *druhý termín* přihlášek.
@endcomponent

@component('mail::table')
    | Přihláška do       | Název akce/závodu        | ORIS ID
    | :----------------- |:------------- |:------------- |
    @foreach ($eventSecondDateEnd as $secondDate)
        | {{ Carbon::parse($secondDate->entry_date_2)->format('d.m.Y - H:i') }}  | {{$secondDate->name }} | @if($secondDate->oris_id !== null)[{{$secondDate->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$secondDate->oris_id}})@endif  |
    @endforeach
@endcomponent
@endif

@if(!is_null($eventThirdDateEnd))

@component('mail::divider')
## 3 termín přihlášek

Závody u kterých končí *třetí termín* přihlášek.
@endcomponent

@component('mail::table')
    | Přihláška do       | Název akce/závodu        | ORIS ID
    | :----------------- |:------------- |:------------- |
    @foreach ($eventThirdDateEnd as $thirdDate)
        | {{ Carbon::parse($thirdDate->entry_date_3)->format('d.m.Y - H:i') }}  | {{$thirdDate->name }} | @if($thirdDate->oris_id !== null)[{{$thirdDate->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$thirdDate->oris_id}})@endif  |
    @endforeach
@endcomponent
@endif

</x-mail::message>
