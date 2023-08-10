<?php
    use Illuminate\Support\Carbon;
    /** @var array{array{fullName: string, email: string, debit: float|int}} $usersData */
?>

<x-mail::message>

## Uživatelé s nízkým kreditem

Měsíční výpis uživatelů {{ Config::get('site-config.club.abbr') }} klubu k dnešnímu dni
{{ Carbon::now()->format('d.m.Y H:i') }}, kteří k prvnímu mají nízký kredit na kontě.

Prosím o kontrolu s následou informací k uživatelům:

@component('mail::table')
    | Uživatel              | Stav konta Kč         |
    | :----------------- |:------------- |
    @foreach ($usersData as $user)
        | {{$user['fullName'] }} ({{$user['email']}})  | {{$user['debit'] }} Kč |
    @endforeach
@endcomponent

Mějte se fajn a jezděte na závody - {{ Config::get('site-config.club.abbr') }}

@component('mail::subcopy')
    Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.
@endcomponent

</x-mail::message>
