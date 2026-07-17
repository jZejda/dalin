@php
    $altNamePart = null;
    if (!is_null($sportEvent->alt_name)) {
        $altNamePart = ' | ' . $sportEvent->alt_name;
    }
@endphp

<x-mail::message>

## {{ __('mail/user-entry-notification.body.heading') }}

@component('mail::divider')
{{ __('mail/user-entry-notification.body.intro', ['name' => $sportEvent->name, 'alt_name' => $altNamePart ?? '']) }}

- {{ __('mail/user-entry-notification.body.date_line', ['date' => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sportEvent->date)->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT) ?? '']) }}
- {{ __('mail/user-entry-notification.body.place_line', ['place' => $sportEvent->place ?? '']) }}
@endcomponent

{{ $content }}

</x-mail::message>
