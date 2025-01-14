<?php

use App\Mail\UserPasswordSend;
use App\Models\User;

/** @var string $newPassword */
/** @var User $user */
/** @var string $action */
?>

<x-mail::message>

## {{ $action === UserPasswordSend::ACTION_SEND_PASSWORD ? 'Nové heslo' : 'Reset hesla'  }} do členské sekce {!!config('site-config.club.abbr')!!}.

Na tomto účtu **{{ $user->name }}** | {{ $user->email }} bylo nyní nastaveno heslo.


**Nové heslo** k přihlášení do portálu na adrese **[{!! \Illuminate\Support\Facades\URL::to('/') !!}/admin/login]({!! \Illuminate\Support\Facades\URL::to('/') !!}/admin/login)**:


@component('mail::divider')
**{{ $newPassword }}**
@endcomponent

### Nápověda

Pro více informací, jak pracovat s aplikací, navštivte prosím naši nápovědu na [této stránce]({{ \App\Shared\Helpers\AppHelper::getPageHelpUrl('') }}).

#### Mohlo by se hodit

Heslo si nejlépe ulož do některého důvěryhodného správce hesel jako například [KeePassXC](https://keepassxc.org/),
[BitWarden](https://bitwarden.com/) a pod.

</x-mail::message>
