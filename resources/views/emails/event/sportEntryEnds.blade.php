@php
    use App\Shared\Helpers\EmptyType;
    use App\Services\OrisApiService;
    use Illuminate\Support\Str;
@endphp

<x-mail::message>

## {{ __('mail/event-entry-ends.body.heading') }}

{{ __('mail/event-entry-ends.body.intro', ['days' => $daysBefore]) }}

@component('mail::table')
    | {{ __('mail/event-entry-ends.body.table_entry_until') }}       | {{ __('mail/event-entry-ends.body.table_event_name') }}        | {{ __('mail/event-entry-ends.body.table_oris_id') }}
    | :----------------- |:------------- |:------------- |
    @foreach ($sportEventContent as $sportEvent)
        | {{\Carbon\Carbon::parse($sportEvent->entry_date_1)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT)}}  | {{$sportEvent->name }}<br>@if(EmptyType::stringNotEmpty($sportEvent->alt_name)) {{Str::limit($sportEvent->alt_name, 35)}}@endif   | @if($sportEvent->oris_id !== null)[{{$sportEvent->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$sportEvent->oris_id}})@endif  |
    @endforeach
@endcomponent

</x-mail::message>
