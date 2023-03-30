@php
    use Illuminate\Support\Carbon;
@endphp

<script src="https://cdn.tailwindcss.com"></script>
@vite(['resources/css/app.css'])

<x-filament::page>
    <section class="bg-white dark:bg-gray-900">
        <div class="py-4 px-4">
            <h2 class="mb-2 text-2xl tracking-tight font-extrabold text-gray-900 dark:text-white">{{ $record->name }}</h2>
            <p class="mb-2 text-gray-500 dark:text-gray-400">{{ $record->alt_name }}</p>
            <p class="mb-2 text-success-700">{{ $record->event_info }}</p>
            <p class="mb-2 text-danger-600">{{ $record->event_warning }}</p>
            <div class="grid pt-2 text-left border-t border-gray-200 md:gap-16 dark:border-gray-700 md:grid-cols-2">
                <div>
                    <div class="mb-10">
                        <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                            Termíny
                        </h3>
                        <div class="text-gray-500 dark:text-gray-400 px-2">
                            <ul class="ml-2">
                                @if ($record->date !== null)
                                    <li>Datum konání: <strong>{{ $record->date->format('d. m. Y') }}</strong></li>
                                @endif
                                @if ($record->entry_date_1 !== null)
                                    <li>První termín: <strong>{{ $record->entry_date_1->format('d. m. Y H:i') }}</strong></li>
                                @endif
                                @if ($record->entry_date_2 !== null)
                                    <li>Druhý termín: <strong>{{ $record->entry_date_2->format('d. m. Y H:i') }}</strong></li>
                                @endif
                                @if ($record->entry_date_3 !== null)
                                    <li>Třetí termín: <strong>{{ $record->entry_date_3->format('d. m. Y H:i') }}</strong></li>
                                @endif
                            </ul>
                        </div>
                        <h3 class="mt-4 flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                            Ostatní informace
                        </h3>
                        <div class="text-gray-500 dark:text-gray-400 px-2">
                            <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Default</span>

                            <ul class="ml-2">
                                <li>Místo konání: {{ $record->place }}</li>
                                <li>ORIS id: {{ $record->oris_id }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mb-10">
                        <div class="app-front-content">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                Popis akce
                            </h3>
                            <p>{{ Markdown::parse($record->entry_desc) }}</p>
                        </div>
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
