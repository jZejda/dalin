<x-mail::message>

## Konec přihlášek - 1 termín

Blíží ze konec přihlášek na závody vypasané níže. Do termínu přihlášení zbývají necelé **{{ $daysBefore }} dny**.
Příhlášení proveď podle pokynů v administraci.

@component('mail::table')
    | Přihláška do       | Název akce/závodu        |
    | :----------------- |:------------- |
    @foreach ($sportEventContent as $sportEvent)
        | {{  \Carbon\Carbon::parse($sportEvent->entry_date_1)->format('d.m.Y - H:i') }}  | {{$sportEvent->name }}  |
    @endforeach
@endcomponent

Mějte se fajn a jezděte na závody - ABM

@component('mail::subcopy')
    Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.
@endcomponent

</x-mail::message>
