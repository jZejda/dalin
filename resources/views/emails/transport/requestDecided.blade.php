<x-mail::message>

@if($transportRequest->isApproved())
## {{ __('transport.mail.request_decided_heading_approved') }}
@else
## {{ __('transport.mail.request_decided_heading_rejected') }}
@endif

@component('mail::divider')
{{ __('transport.mail.request_decided_race_label') }}: **{{ $transportRequest->transportOffer?->sportEvent?->name }}**

- {{ __('transport.mail.request_decided_driver_label') }}: **{{ $transportRequest->transportOffer?->user?->name }}**
- {{ __('transport.direction') }}: **{{ $transportRequest->direction->label() }}**
- {{ __('transport.seats') }}: **{{ $transportRequest->seats }}**
- {{ __('transport.departure_place') }}: **{{ $transportRequest->transportOffer?->departure_place }}**
@endcomponent

@if($transportRequest->isApproved())
{{ __('transport.mail.request_decided_footer_approved') }}
@else
{{ __('transport.mail.try_another_offer_footer') }}
@endif

</x-mail::message>
