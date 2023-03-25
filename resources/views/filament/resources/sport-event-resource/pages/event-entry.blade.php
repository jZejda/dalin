<x-filament::page>
    <section class="bg-white dark:bg-gray-900">
        <div class="py-4 px-4 rounded-t-xl mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <h2 class="mb-8 text-2xl tracking-tight font-extrabold text-gray-900 dark:text-white">{{ $record->name }}</h2>
            <p class="mb-4 text-gray-500 dark:text-gray-400">{{ $record->alt_name }}</p>
            <div class="grid pt-2 text-left border-t border-gray-200 md:gap-16 dark:border-gray-700 md:grid-cols-2">
                <div>
                    <div class="mb-10">
                        <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                            Termíny
                        </h3>
                        <div class="text-gray-500 dark:text-gray-400 px-2">
                            <ul class="ml-2">
                                <li>Datum konání: {{ $record->date }}</li>
                                <li>První termín: {{ $record->entry_date_1 }}</li>
                                <li>Druhý termín: {{ $record->entry_date_2 }}</li>
                                <li>Třetí termín: {{ $record->entry_date_3 }}</li>
                            </ul>
                        </div>
                        <h3 class="mt-4 flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                            Ostatní informace
                        </h3>
                        <div class="text-gray-500 dark:text-gray-400 px-2">
                            <ul class="ml-2">
                                <li>Místo konání: {{ $record->place }}</li>
                                <li>ORIS id: {{ $record->oris_id }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mb-10">
                        <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                            Popis akce
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">{{ $record->entry_desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}

{{--        <div class="flex flex-wrap items-center gap-4 justify-start">--}}
{{--            <x-filament::button type="submit">--}}
{{--                Načti ORIS 999--}}
{{--            </x-filament::button>--}}

{{--            <x-filament::button type="button" color="secondary" tag="a" :href="$this->back_button_url">--}}
{{--                Zpět--}}
{{--            </x-filament::button>--}}
{{--        </div>--}}
    </form>

    <div>
        {{ $this->table }}
    </div>

</x-filament::page>
