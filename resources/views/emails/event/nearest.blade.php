<x-mail::message>

## {{ __('mail/sport-event-nearest.body.heading') }}

{{ __('mail/sport-event-nearest.body.intro') }}

@component('mail::table')
    | {{ __('mail/sport-event-nearest.body.table_entry_until') }}       | {{ __('mail/sport-event-nearest.body.table_event_name') }}        |
    | :----------------- |:------------- |
    @foreach ($sportEvents as $sportEvent)
        | {{  \Carbon\Carbon::parse($sportEvent->date)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}  | {{$sportEvent->name }}  |
    @endforeach
@endcomponent

{{ __('mail/sport-event-nearest.body.signoff', ['club' => Config::get('site-config.club.abbr')]) }}

@component('mail::subcopy')
    {{ __('mail/sport-event-nearest.body.unsubscribe_note') }}
@endcomponent

</x-mail::message>
