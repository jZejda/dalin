<?php

use App\Mail\UserPasswordSend;
use App\Models\User;

/** @var string $newPassword */
/** @var User $user */
/** @var string $action */
?>

<x-mail::message>

## {{ $action === UserPasswordSend::ACTION_SEND_PASSWORD ? 'Heslo' : 'Reset hesla'  }} do členské sekce - {!!config('site-config.club.abbr')!!}.

{{ $action === UserPasswordSend::ACTION_SEND_PASSWORD ? 'Byl vám vytvořen nový účet ' : 'Bylo vám resetováno heslo '  }} v přihláškovém systému oddílu {!!config('site-config.club.abbr')!!}.

@component('mail::divider')
Adresa: **[{!! \Illuminate\Support\Facades\URL::to('/') !!}/admin/login]({!! \Illuminate\Support\Facades\URL::to('/') !!}/admin/login)**

Jméno: **{{ $user->name }}**

Login: **{{ $user->email }}**

Heslo: **{{ $newPassword }}**


@endcomponent

### Nápověda / Řešení potíží

Pro více informací, jak pracovat s aplikací, navštivte prosím naši nápovědu na [této stránce]({{ \App\Shared\Helpers\AppHelper::getPageHelpUrl('') }}).
V případě problémů s přihlášením, směřujte případné dotazy na email {!! config('site-config.club.technical_email') !!}.
Vygenerované heslo si lze po přihlášení v systému změnit.

#### Mohlo by se hodit

Heslo si nejlépe ulož do některého důvěryhodného správce hesel jako například [KeePassXC](https://keepassxc.org/),
[BitWarden](https://bitwarden.com/) a pod.

</x-mail::message>
