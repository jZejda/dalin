<?php

use Illuminate\Support\Collection;
use App\Models\SportEvent;
use Illuminate\Support\Carbon;
use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Str;
use App\Services\OrisApiService;

/** @var SportEvent[]|Collection $eventFirstDateEnd */
/** @var SportEvent[]|Collection $eventSecondDateEnd */
/** @var SportEvent[]|Collection $eventThirdDateEnd */
?>

<x-mail::message>

## {{ __('mail/event-weekly-ends-by-sport.body.heading') }}

{{ __('mail/event-weekly-ends-by-sport.body.intro', [
    'from' => Carbon::now()->addDay()->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT),
    'to' => Carbon::now()->addDays(8)->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT),
]) }}

@if(!is_null($eventFirstDateEnd))
@component('mail::divider')
## {{ __('mail/event-weekly-ends-by-sport.body.first_term_heading') }}

{{ __('mail/event-weekly-ends-by-sport.body.first_term_intro') }}
@endcomponent

@component('mail::table')
| {{ __('mail/event-weekly-ends-by-sport.body.table_entry_until') }}       | {{ __('mail/event-weekly-ends-by-sport.body.table_event_date') }}         | {{ __('mail/event-weekly-ends-by-sport.body.table_event_name') }}   |
| :----------------- |:------------------ |:------------------ |
@foreach ($eventFirstDateEnd as $firstDate)
| {{ Carbon::parse($firstDate->entry_date_1)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }} | @if(EmptyType::stringNotEmpty($firstDate->alt_name)){{ Carbon::parse($firstDate->date)->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT) }}@endif | @if($firstDate->oris_id !== null)[{{$firstDate->name}}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$firstDate->oris_id}})@endif<br>@if(EmptyType::stringNotEmpty($firstDate->alt_name)){{Str::limit($firstDate->alt_name, 35)}}@endif | @if($firstDate->oris_id !== null)[{{$firstDate->oris_id }}](https://oris.orientacnisporty.cz/Zavod?id={{$firstDate->oris_id}})@endif  |
@endforeach
@endcomponent
@endif

@if(!is_null($eventSecondDateEnd))

@component('mail::divider')
## {{ __('mail/event-weekly-ends-by-sport.body.second_term_heading') }}

{{ __('mail/event-weekly-ends-by-sport.body.second_term_intro') }}
@endcomponent

@component('mail::table')
| {{ __('mail/event-weekly-ends-by-sport.body.table_entry_until') }}       | {{ __('mail/event-weekly-ends-by-sport.body.table_event_date') }}         | {{ __('mail/event-weekly-ends-by-sport.body.table_event_name') }}   |
| :----------------- |:------------------ |:------------------ |
@foreach ($eventSecondDateEnd as $secondDate)
| {{ Carbon::parse($secondDate->entry_date_2)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }} | @if(EmptyType::stringNotEmpty($secondDate->alt_name)){{ Carbon::parse($secondDate->date)->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT) }}@endif | @if($secondDate->oris_id !== null) [{{$secondDate->name}}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$secondDate->oris_id}})@endif<br>@if(EmptyType::stringNotEmpty($secondDate->alt_name)){{Str::limit($secondDate->alt_name, 35)}}@endif | @if($secondDate->oris_id !== null)[{{$secondDate->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$secondDate->oris_id}})@endif  |
@endforeach

@endcomponent
@endif

@if(!is_null($eventThirdDateEnd))

@component('mail::divider')
## {{ __('mail/event-weekly-ends-by-sport.body.third_term_heading') }}

{{ __('mail/event-weekly-ends-by-sport.body.third_term_intro') }}
@endcomponent

@component('mail::table')
| {{ __('mail/event-weekly-ends-by-sport.body.table_entry_until') }}       | {{ __('mail/event-weekly-ends-by-sport.body.table_event_date') }}         | {{ __('mail/event-weekly-ends-by-sport.body.table_event_name') }}   |
| :----------------- |:------------------ |:------------------ |
@foreach ($eventThirdDateEnd as $thirdDate)
| {{ Carbon::parse($thirdDate->entry_date_3)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }} | @if(EmptyType::stringNotEmpty($thirdDate->alt_name)) {{ Carbon::parse($thirdDate->date)->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT) }}@endif | @if($thirdDate->oris_id !== null) [{{$thirdDate->name}}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$thirdDate->oris_id}})@endif<br>@if(EmptyType::stringNotEmpty($thirdDate->alt_name)){{Str::limit($thirdDate->alt_name, 35)}}@endif | @if($thirdDate->oris_id !== null)[{{$thirdDate->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$thirdDate->oris_id}})@endif  |
@endforeach
@endcomponent
@endif

</x-mail::message>
