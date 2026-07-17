<x-mail::message>

## {{ __('transport.mail.request_cancelled_heading') }}

@component('mail::divider')
{{ __('transport.mail.request_cancelled_intro', ['passenger' => $transportRequest->user?->name, 'event' => $transportRequest->transportOffer?->sportEvent?->name]) }}

- {{ __('transport.direction') }}: **{{ $transportRequest->direction->label() }}**
- {{ __('transport.mail.request_cancelled_seats_label') }}: **{{ $transportRequest->seats }}**
@endcomponent

{{ __('transport.mail.request_cancelled_footer') }}

</x-mail::message>
