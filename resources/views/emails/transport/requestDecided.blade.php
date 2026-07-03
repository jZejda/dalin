<x-mail::message>

@if($transportRequest->isApproved())
## Tvoje žádost o spolujízdu byla schválena 🎉
@else
## Tvoje žádost o spolujízdu byla zamítnuta
@endif

@component('mail::divider')
Závod: **{{ $transportRequest->transportOffer?->sportEvent?->name }}**

- Řidič: **{{ $transportRequest->transportOffer?->user?->name }}**
- Směr: **{{ $transportRequest->direction->label() }}**
- Počet míst: **{{ $transportRequest->seats }}**
- Odkud: **{{ $transportRequest->transportOffer?->departure_place }}**
@endcomponent

@if($transportRequest->isApproved())
Místo v autě je pro tebe rezervované. Detaily domluv přímo s řidičem.
@else
Zkus jinou nabídku dopravy na stránce Doprava u závodu.
@endif

</x-mail::message>
