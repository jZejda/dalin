<?php

use App\Mail\UserPasswordSend;
use App\Models\User;

/** @var string $newPassword */
/** @var User $user */
/** @var string $action */
?>

<x-mail::message>

## {{ $action === UserPasswordSend::ACTION_SEND_PASSWORD ? 'Nové heslo' : 'Reset hesla'  }} k portálu

Na tomto účtu **{{ $user->name }}** | {{ $user->email }} bylo nyní nastaveno nové heslo.

Nové heslo k přihlášení do portálu:


@component('mail::divider')
**{{ $newPassword }}**
@endcomponent

Heslo si nejlépe ulož do některého důvěryhodného správce hesel jako například [KeePassXC](https://keepassxc.org/),
[BitWarden](https://bitwarden.com/) a pod.

</x-mail::message>
