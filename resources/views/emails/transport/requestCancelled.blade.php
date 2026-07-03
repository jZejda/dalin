<x-mail::message>

## Spolujezdec zrušil rezervaci

@component('mail::divider')
**{{ $transportRequest->user?->name }}** zrušil svou rezervaci ve tvé nabídce dopravy na závod
**{{ $transportRequest->transportOffer?->sportEvent?->name }}**.

- Směr: **{{ $transportRequest->direction->label() }}**
- Počet uvolněných míst: **{{ $transportRequest->seats }}**
@endcomponent

Místa jsou opět volná pro další zájemce.

</x-mail::message>
