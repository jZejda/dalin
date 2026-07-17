<?php

use App\Mail\UserPasswordSend;
use App\Models\User;
use Illuminate\Support\Facades\URL;

/** @var string $newPassword */
/** @var User $user */
/** @var string $action */

$isNewAccount = $action === UserPasswordSend::ACTION_SEND_PASSWORD;
$loginUrl = URL::to('/') . '/admin/login';
$club = config('site-config.club.abbr');
?>

<x-mail::message>

@if ($isNewAccount)
## {{ __('mail/user-password-send.body.heading_new_account', ['club' => $club]) }}

{{ __('mail/user-password-send.body.intro_new_account', ['club' => $club]) }}
@else
## {{ __('mail/user-password-send.body.heading_reset', ['club' => $club]) }}

{{ __('mail/user-password-send.body.intro_reset', ['club' => $club]) }}
@endif

@component('mail::divider')
{{ __('mail/user-password-send.body.address_label') }}: **[{{ $loginUrl }}]({{ $loginUrl }})**

{{ __('mail/user-password-send.body.name_label') }}: **{{ $user->name }}**

{{ __('mail/user-password-send.body.login_label') }}: **{{ $user->email }}**

{{ __('mail/user-password-send.body.password_label') }}: **{{ $newPassword }}**


@endcomponent

### {{ __('mail/user-password-send.body.help_heading') }}

{{ __('mail/user-password-send.body.help_text', ['url' => \App\Shared\Helpers\AppHelper::getPageHelpUrl('')]) }}
{{ __('mail/user-password-send.body.help_login_issues', ['email' => config('site-config.club.technical_email')]) }}
{{ __('mail/user-password-send.body.help_password_change') }}

#### {{ __('mail/user-password-send.body.tips_heading') }}

{{ __('mail/user-password-send.body.tips_text') }}

</x-mail::message>
