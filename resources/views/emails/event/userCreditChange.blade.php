@php
    use App\Shared\Helpers\EmptyType;
    use Illuminate\Support\Str;

    /** @var App\Models\User $user */
    /** @var App\Models\UserCredit $userCredit */
@endphp

<x-mail::message>

## Pohyb na účtu

Na účtu uživatele došlo k pohybu. 

Aktuálně **{{ Carbon::now()->format('d.m.Y H:i') }}** se nacház na účtu uživatele {{ $user->getParam(UserParamType::UserActualBalance) }} Kč.

### Pohyb z poslední transakce

@component('mail::divider')
## Položka

- částka: {{ $userCredit->amount }}
- datum transakce: {{ Carbon::parse($userCredit->created_at)->format('d.m.Y H:i') }}    
- ID transakce: {{ $userCredit->id }}
- vazba na ID bankovní transakce: {{ $userCredit->bank_transaction_id }}
 
@endcomponent

Mějte se fajn a jezděte na závody - {{ Config::get('site-config.club.abbr') }}

</x-mail::message>
