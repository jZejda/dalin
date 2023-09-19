<x-mail::message>

## Konec přihlášek - 1 termín

Blíží ze konec přihlášek na závody vypasané níže. Do termínu přihlášení zbývají necelé **{{ $daysBefore }} dny**.
Příhlášení proveď podle pokynů v administraci.

@component('mail::table')
    | Přihláška do       | Název akce/závodu        | ORIS ID
    | :----------------- |:------------- |:------------- |
    @foreach ($sportEventContent as $sportEvent)
        | {{  \Carbon\Carbon::parse($sportEvent->entry_date_1)->format('d.m.Y - H:i') }}  | {{$sportEvent->name }}  | @if($sportEvent->oris_id !== null)[{{$sportEvent->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$sportEvent->oris_id}})@endif  |
    @endforeach
@endcomponent

</x-mail::message>
