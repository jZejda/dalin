<?php
use Illuminate\Support\Carbon;
?>

<x-mail::message>

## Reset Hesla k portálu

Na tomto účtu **{{ $user->name }}** | {{ $user->email }} bylo nyní nastaveno nové heslo.

Nové heslo k přihlášení do portálu:


@component('mail::divider')
**{{ $newPassword }}**
@endcomponent

Heslo si nejlépe ulož do některého důvěryhodného správce hesel jako například [KeePassXC](https://keepassxc.org/), [BitWarden](https://bitwarden.com/) a pod.

</x-mail::message>
