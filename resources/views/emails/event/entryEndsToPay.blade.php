<?php

use App\Models\SportEvent;
use App\Services\OrisApiService;
use Illuminate\Support\Carbon;

/** @var SportEvent[] $sportEvents */
/** @var int $deadline */
?>

<x-mail::message>

## {{ __('mail/entry-ends-to-pay.body.heading') }}

{{ __('mail/entry-ends-to-pay.body.intro', ['deadline' => $deadline]) }}

@if(!is_null($sportEvents))
@component('mail::divider')
## {{ __('mail/entry-ends-to-pay.body.deadline_heading', ['deadline' => $deadline]) }}

{{ __('mail/entry-ends-to-pay.body.deadline_text', ['deadline' => $deadline]) }}
@endcomponent

@component('mail::table')
    | {{ __('mail/entry-ends-to-pay.body.table_entry_until') }}       | {{ __('mail/entry-ends-to-pay.body.table_event_name') }}        | {{ __('mail/entry-ends-to-pay.body.table_oris_id') }}
    | :----------------- |:------------- |:------------- |
    @foreach ($sportEvents as $event)
        @if ($deadline === 1)
        | {{ Carbon::parse($event->entry_date_1)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}  | {{$event->name }} | @if($event->oris_id !== null)[{{$event->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$event->oris_id}})@endif  |
        @elseif($deadline === 2)
        | {{ Carbon::parse($event->entry_date_2)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}  | {{$event->name }} | @if($event->oris_id !== null)[{{$event->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$event->oris_id}})@endif  |
        @elseif($deadline === 3)
        | {{ Carbon::parse($event->entry_date_3)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}  | {{$event->name }} | @if($event->oris_id !== null)[{{$event->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$event->oris_id}})@endif  |
        @endif
    @endforeach
@endcomponent
@endif

</x-mail::message>
