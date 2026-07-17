<x-mail::message>

## {{ __('transport.mail.offer_cancelled_heading') }}

@component('mail::divider')
{{ __('transport.mail.offer_cancelled_intro', ['driver' => $transportRequest->transportOffer?->user?->name, 'event' => $transportRequest->transportOffer?->sportEvent?->name]) }}

- {{ __('transport.direction') }}: **{{ $transportRequest->direction->label() }}**
- {{ __('transport.seats') }}: **{{ $transportRequest->seats }}**
@endcomponent

{{ __('transport.mail.try_another_offer_footer') }}

</x-mail::message>
