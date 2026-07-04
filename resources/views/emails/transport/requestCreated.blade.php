<x-mail::message>

## Nová žádost o spolujízdu

@component('mail::divider')
**{{ $transportRequest->user?->name }}** má zájem o místo ve tvé nabídce dopravy na závod
**{{ $transportRequest->transportOffer?->sportEvent?->name }}**.

- Směr: **{{ $transportRequest->direction->label() }}**
- Počet míst: **{{ $transportRequest->seats }}**
- Odkud: **{{ $transportRequest->transportOffer?->departure_place }}**
- Vozidlo: **{{ $transportRequest->transportOffer?->vehicle?->name }}**
@endcomponent

Žádost můžeš vyřídit rovnou z tohoto e-mailu:

<x-mail::button :url="$approveUrl" color="success">
Schválit žádost
</x-mail::button>

<x-mail::button :url="$rejectUrl" color="error">
Zamítnout žádost
</x-mail::button>

Odkazy platí do dne konání závodu. Žádosti najdeš i v aplikaci na stránce Doprava u závodu.

</x-mail::message>
