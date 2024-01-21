@php
    use App\Shared\Helpers\EmptyType;
    use Illuminate\Support\Str;
@endphp

<x-mail::message>

## Změny v seznamů závodů

Vybrané informace o změnách v přihláškovém systému {{ Config::get('site-config.club.abbr') }}.

Do systemu byly přidány závody:

@component('mail::table')
    | Závod              | Přihláska do         |
    | :----------------- |:------------- |
    @foreach ($sportEvents as $sportEvent)
        | {{$sportEvent->name }}<br>@if(EmptyType::stringNotEmpty($sportEvent->alt_name)) {{Str::limit($sportEvent->alt_name, 35)}}@endif  | {{\Carbon\Carbon::parse($sportEvent->date)->format('Y.m.d H:i')}}  |
    @endforeach
@endcomponent

@component('mail::divider')

## Pokus

Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.
@endcomponent

Mějte se fajn a jezděte na závody - {{ Config::get('site-config.club.abbr') }}

</x-mail::message>
