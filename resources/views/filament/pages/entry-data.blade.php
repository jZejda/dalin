<x-filament::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-4 justify-start">
            <x-filament::button type="submit">
                Načti ORIS
            </x-filament::button>

            <x-filament::button type="button" color="secondary" tag="a" :href="$this->cancel_button_url">
                Cancel
            </x-filament::button>
        </div>
    </form>

    <div>
        <p>Přihlášky členů</p>
    </div>

    <div>
        {{ $this->table }}
    </div>

{{--    @foreach($last_files as $file)--}}
{{--        {{ $file }}--}}
{{--    @endforeach--}}

</x-filament::page>
