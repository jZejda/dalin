<x-mail::message>

## Nabídka dopravy byla zrušena

@component('mail::divider')
Řidič **{{ $transportRequest->transportOffer?->user?->name }}** zrušil nabídku dopravy na závod
**{{ $transportRequest->transportOffer?->sportEvent?->name }}**, ve které jsi měl žádost o místo.

- Směr: **{{ $transportRequest->direction->label() }}**
- Počet míst: **{{ $transportRequest->seats }}**
@endcomponent

Zkus jinou nabídku dopravy na stránce Doprava u závodu.

</x-mail::message>
