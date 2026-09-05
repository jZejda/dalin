<x-filament::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-4 justify-start">
            <x-filament::button type="submit">
                {{ __('user-profile-settings.form.submit') }}
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
