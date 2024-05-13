<?php

use Illuminate\Support\Collection;
use App\Models\SportEvent;
use Illuminate\Support\Carbon;
use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Str;

/** @var SportEvent[]|Collection $eventFirstDateEnd */
/** @var SportEvent[]|Collection $eventSecondDateEnd */
/** @var SportEvent[]|Collection $eventThirdDateEnd */
?>

<x-mail::message>

## Konec přihlášek

Týdenní souhrn přihlášek závodů vypsaných níže. Závody jsou rozděleny podle termínu přihlášek v týdnu
 **{{ Carbon::now()->addDay()->format('d.m.Y') }}** - **{{ Carbon::now()->addDays(8)->format('d.m.Y') }}**.

@if(!is_null($eventFirstDateEnd))
@component('mail::divider')
## 1 termín přihlášek

Závody u kterých končí **první termín** přihlášek.
@endcomponent

@component('mail::table')
| Přihláška do       | Datum akce         | Název akce/závodu   |
| :----------------- |:------------------ |:------------------ |
@foreach ($eventFirstDateEnd as $firstDate)
| {{ Carbon::parse($firstDate->entry_date_1)->format('d.m.Y - H:i') }} | @if(EmptyType::stringNotEmpty($firstDate->alt_name)){{ Carbon::parse($firstDate->date)->format('d.m.Y') }}@endif | @if($firstDate->oris_id !== null)[{{$firstDate->name}}](https://oris.orientacnisporty.cz/Zavod?id={{$firstDate->oris_id}})@endif<br>@if(EmptyType::stringNotEmpty($firstDate->alt_name)){{Str::limit($firstDate->alt_name, 35)}}@endif | @if($firstDate->oris_id !== null)[{{$firstDate->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$firstDate->oris_id}})@endif  |
@endforeach
@endcomponent
@endif

@if(!is_null($eventSecondDateEnd))

@component('mail::divider')
## 2 termín přihlášek

Závody u kterých končí **druhý termín** přihlášek.
@endcomponent

@component('mail::table')
| Přihláška do       | Datum akce         | Název akce/závodu   |
| :----------------- |:------------------ |:------------------ |
@foreach ($eventSecondDateEnd as $secondDate)
| {{ Carbon::parse($secondDate->entry_date_2)->format('d.m.Y - H:i') }} | @if(EmptyType::stringNotEmpty($secondDate->alt_name)){{ Carbon::parse($secondDate->date)->format('d.m.Y') }}@endif | @if($secondDate->oris_id !== null) [{{$secondDate->name}}](https://oris.orientacnisporty.cz/Zavod?id={{$secondDate->oris_id}})@endif<br>@if(EmptyType::stringNotEmpty($secondDate->alt_name)){{Str::limit($secondDate->alt_name, 35)}}@endif | @if($secondDate->oris_id !== null)[{{$secondDate->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$secondDate->oris_id}})@endif  |
@endforeach

@endcomponent
@endif

@if(!is_null($eventThirdDateEnd))

@component('mail::divider')
## 3 termín přihlášek

Závody u kterých končí **třetí termín** přihlášek.
@endcomponent

@component('mail::table')
| Přihláška do       | Datum akce         | Název akce/závodu   |
| :----------------- |:------------------ |:------------------ |
@foreach ($eventThirdDateEnd as $thirdDate)
| {{ Carbon::parse($thirdDate->entry_date_3)->format('d.m.Y - H:i') }} | @if(EmptyType::stringNotEmpty($thirdDate->alt_name)) {{ Carbon::parse($thirdDate->date)->format('d.m.Y') }}@endif | @if($thirdDate->oris_id !== null) [{{$thirdDate->name}}](https://oris.orientacnisporty.cz/Zavod?id={{$thirdDate->oris_id}})@endif<br>@if(EmptyType::stringNotEmpty($thirdDate->alt_name)){{Str::limit($thirdDate->alt_name, 35)}}@endif | @if($thirdDate->oris_id !== null)[{{$thirdDate->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$thirdDate->oris_id}})@endif  |
@endforeach
@endcomponent
@endif

</x-mail::message>
