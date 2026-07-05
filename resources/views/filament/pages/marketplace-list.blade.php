<x-filament::page>
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$activeTab === 'active'"
            wire:click="setTab('active')"
            icon="heroicon-o-megaphone"
        >
            {{ __('marketplace.tab_active') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$activeTab === 'past'"
            wire:click="setTab('past')"
            icon="heroicon-o-archive-box"
        >
            {{ __('marketplace.tab_past') }}
        </x-filament::tabs.item>
    </x-filament::tabs>

    {{ $this->table }}
</x-filament::page>
