<div class="space-y-4">
    {{ $this->table }}

    @livewire(\App\Livewire\SportEvent\RequestsForMyOffersList::class, ['sportEvent' => $sportEvent], key('requests-for-my-offers-'.$sportEvent->id))
    @livewire(\App\Livewire\SportEvent\MyTransportRequestsList::class, ['sportEvent' => $sportEvent], key('my-transport-requests-'.$sportEvent->id))
</div>
