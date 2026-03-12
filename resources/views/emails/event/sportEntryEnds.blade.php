@php
    use App\Shared\Helpers\EmptyType;
    use App\Services\OrisApiService;
    use Illuminate\Support\Str;
@endphp

<x-mail::message>

## Konec přihlášek - 1 termín

Blíží ze konec přihlášek na závody vypasané níže. Do termínu přihlášení zbývají necelé **{{ $daysBefore }} dny**.
Příhlášení proveď podle pokynů v administraci.

@component('mail::table')
    | Přihláška do       | Název akce/závodu        | ORIS ID
    | :----------------- |:------------- |:------------- |
    @foreach ($sportEventContent as $sportEvent)
        | {{\Carbon\Carbon::parse($sportEvent->entry_date_1)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT)}}  | {{$sportEvent->name }}<br>@if(EmptyType::stringNotEmpty($sportEvent->alt_name)) {{Str::limit($sportEvent->alt_name, 35)}}@endif   | @if($sportEvent->oris_id !== null)[{{$sportEvent->oris_id }}]({{ OrisApiService::ORIS_URL }}/Zavod?id={{$sportEvent->oris_id}})@endif  |
    @endforeach
@endcomponent

</x-mail::message>
