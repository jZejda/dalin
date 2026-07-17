<x-mail::message>

## {{ __('transport.mail.request_created_heading') }}

@component('mail::divider')
{{ __('transport.mail.request_created_intro', ['passenger' => $transportRequest->user?->name, 'event' => $transportRequest->transportOffer?->sportEvent?->name]) }}

- {{ __('transport.direction') }}: **{{ $transportRequest->direction->label() }}**
- {{ __('transport.seats') }}: **{{ $transportRequest->seats }}**
- {{ __('transport.departure_place') }}: **{{ $transportRequest->transportOffer?->departure_place }}**
- {{ __('transport.vehicle') }}: **{{ $transportRequest->transportOffer?->vehicle?->name }}**
@endcomponent

{{ __('transport.mail.request_created_cta') }}

<x-mail::button :url="$approveUrl" color="success">
{{ __('transport.mail.request_created_approve_button') }}
</x-mail::button>

<x-mail::button :url="$rejectUrl" color="error">
{{ __('transport.mail.request_created_reject_button') }}
</x-mail::button>

{{ __('transport.mail.request_created_footer') }}

</x-mail::message>
