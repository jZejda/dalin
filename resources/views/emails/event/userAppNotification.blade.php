<x-mail::message>

## {{ __('mail/user-app-notification.body.heading') }}

{{ __('mail/user-app-notification.body.intro', ['abbr' => Config::get('site-config.club.abbr')]) }}

@component('mail::divider')
{{ __('mail/user-app-notification.body.from_user', ['name' => $user->name]) }}

- {{ __('mail/user-app-notification.body.sent_at', ['date' => \Carbon\Carbon::now()->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) ?? '']) }}
@endcomponent

{{ $content }}

</x-mail::message>
