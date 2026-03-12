<x-mail::message>

## Konec přihlášek - 1 termín

Blíží se konec přihlášek na závody vypsané níže. Do termínu přihlášení zbývají necelé **dva dny**.
Přihlášení proveď podle pokynů v administraci.

@component('mail::table')
    | Přihláška do       | Název akce/závodu        |
    | :----------------- |:------------- |
    @foreach ($sportEvents as $sportEvent)
        | {{  \Carbon\Carbon::parse($sportEvent->date)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}  | {{$sportEvent->name }}  |
    @endforeach
@endcomponent

Mějte se fajn a jezděte na závody - ABM

@component('mail::subcopy')
    Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.
@endcomponent

</x-mail::message>
