@php
    use App\Shared\Helpers\EmptyType;
    use Illuminate\Support\Str;
@endphp

<x-mail::message>

## {{ __('mail/add-update-sport-event.body.heading') }}

{{ __('mail/add-update-sport-event.body.intro', ['club' => Config::get('site-config.club.abbr')]) }}

{{ __('mail/add-update-sport-event.body.events_intro') }}

@component('mail::table')
    | {{ __('mail/add-update-sport-event.body.table_event') }}              | {{ __('mail/add-update-sport-event.body.table_entry_until') }}         |
    | :----------------- |:------------- |
    @foreach ($sportEvents as $sportEvent)
        | {{$sportEvent->name }}<br>@if(EmptyType::stringNotEmpty($sportEvent->alt_name)) {{Str::limit($sportEvent->alt_name, 35)}}@endif  | {{\Carbon\Carbon::parse($sportEvent->date)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT)}}  |
    @endforeach
@endcomponent

@component('mail::divider')

## {{ __('mail/add-update-sport-event.body.divider_heading') }}

{{ __('mail/add-update-sport-event.body.unsubscribe_note') }}
@endcomponent

{{ __('mail/add-update-sport-event.body.signoff', ['club' => Config::get('site-config.club.abbr')]) }}

</x-mail::message>
