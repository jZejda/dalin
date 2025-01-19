@php
use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Enums\UserParamType;

/** @var App\Models\User $user */
/** @var App\Models\UserCredit $userCredit */
@endphp

<x-mail::message>

## Pohyb na účtu

Na účtu uživatele **{{ $user->name }}** došlo k pohybu.

Aktuální výše uživatelského konta: **{{ $user->getParam(UserParamType::UserActualBalance) }}** Kč k datu **{{ Carbon::now()->format('d.m.Y H:i') }}**.

@component('mail::divider')
## Pohyb z poslední transakce

- částka: **{{ $userCredit->amount }} Kč**
- datum transakce: {{ Carbon::parse($userCredit->created_at)->format('d.m.Y H:i') }}
- ID transakce: {{ $userCredit->id }}
- vazba na ID bankovní transakce: {{ $userCredit->bank_transaction_id }}

@endcomponent

Pokud by byla v transakci nějaká nesrovnalost, prosím kontaktujte nás na e-mailu: {!! config('site-config.club.technical_email') !!}

Mějte se fajn a jezděte na závody - {{ Config::get('site-config.club.abbr') }}

</x-mail::message>
