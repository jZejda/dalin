<div class="space-y-4">
    {{ $this->table }}

    @if($this->requestsForMyOffers->isNotEmpty())
        <x-filament::section>
            <x-slot name="heading">{{ __('transport.requests_for_my_offers') }}</x-slot>

            <div class="space-y-3">
                @foreach($this->requestsForMyOffers as $request)
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 pb-3 last:border-b-0 last:pb-0 dark:border-gray-700">
                        <div>
                            <span class="font-medium">{{ $request->user?->name }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                — {{ $request->direction->label() }},
                                {{ __('transport.seats') }}: {{ $request->seats }}
                                ({{ $request->transportOffer?->vehicle?->name }})
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-filament::badge :color="$request->status->color()">
                                {{ $request->status->label() }}
                            </x-filament::badge>
                            @if($request->isPending())
                                <x-filament::button size="sm" color="success" wire:click="approveRequest({{ $request->id }})">
                                    {{ __('transport.approve') }}
                                </x-filament::button>
                                <x-filament::button size="sm" color="danger" wire:click="rejectRequest({{ $request->id }})">
                                    {{ __('transport.reject') }}
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    @if($this->myRequests->isNotEmpty())
        <x-filament::section>
            <x-slot name="heading">{{ __('transport.my_requests') }}</x-slot>

            <div class="space-y-3">
                @foreach($this->myRequests as $request)
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 pb-3 last:border-b-0 last:pb-0 dark:border-gray-700">
                        <div>
                            <span class="font-medium">{{ $request->transportOffer?->user?->name }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                — {{ $request->direction->label() }},
                                {{ __('transport.seats') }}: {{ $request->seats }}
                                ({{ $request->transportOffer?->vehicle?->name }},
                                {{ __('transport.departure_place') }}: {{ $request->transportOffer?->departure_place }})
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-filament::badge :color="$request->status->color()">
                                {{ $request->status->label() }}
                            </x-filament::badge>
                            @if($request->isPending() || $request->isApproved())
                                <x-filament::button
                                    size="sm"
                                    color="gray"
                                    wire:click="cancelRequest({{ $request->id }})"
                                    wire:confirm="{{ __('transport.cancel_request') }}?"
                                >
                                    {{ __('transport.cancel_request') }}
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</div>
