@php
    use App\Shared\Helpers\EmptyType;
    use App\Models\SportEvent;

    /** @var SportEvent $record */
@endphp

{{--<script src="https://cdn.tailwindcss.com"></script>--}}
{{--@vite(['resources/css/app.css'])--}}

<x-filament::page>
    <section class="bg-white dark:bg-gray-900">
        <div class="py-4 px-4">
            <h2 class="mb-2 text-2xl tracking-tight font-extrabold text-gray-900 dark:text-white">{{ $record->name }}
                @if (EmptyType::intNotEmpty($record->oris_id))
                <span class="font-thin">| {{ $record->oris_id }}</span>
                @endif
            </h2>
            <p class="mb-2 text-gray-500 dark:text-gray-400">{{ $record->alt_name }}</p>

            @if (EmptyType::stringNotEmpty($record->event_info))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ $record->event_info }}
            </div>
            @endif
            @if (EmptyType::stringNotEmpty($record->event_warning))
            <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                {{ $record->event_warning }}
            </div>
            @endif
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
                                    <li class="flex items-center space-x-2 border-b border-gray-400 dark:border-gray-700 border-dotted">
                                        <!-- Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                                            <path d="M16 3l0 4"></path>
                                            <path d="M8 3l0 4"></path>
                                            <path d="M4 11l16 0"></path>
                                            <path d="M8 15h2v2h-2z"></path>
                                        </svg>
                                        <span>{{ $record->date->format('d. m. Y') }}</span>
                                    </li>
                                @endif
                                @if ($record->entry_date_1 !== null)
                                    <li class="flex items-center space-x-2">
                                        <!-- Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-number-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M10 10l2 -2v8"></path>
                                        </svg>
                                        <span>{{ $record->entry_date_1->format('d. m. Y H:i') }}</span>
                                    </li>
                                @endif
                                @if ($record->entry_date_2 !== null)
                                    <li class="flex items-center space-x-2">
                                        <!-- Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-number-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M10 8h3a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 0 -1 1v2a1 1 0 0 0 1 1h3"></path>
                                        </svg>
                                        <span>{{ $record->entry_date_2->format('d. m. Y H:i') }}</span>
                                    </li>
                                @endif
                                @if ($record->entry_date_3 !== null)
                                    <li class="flex items-center space-x-2">
                                        <!-- Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-number-3" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M10 9a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1"></path>
                                        </svg>
                                        <span>{{ $record->entry_date_3->format('d. m. Y H:i') }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <h3 class="mt-4 flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                            Ostatní informace
                        </h3>
                        <div class="text-gray-500 dark:text-gray-400 px-2">


                            <ul class="ml-2">
                                @if (EmptyType::stringNotEmpty($record->place))
                                <li class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 7l6 -3l6 3l6 -3l0 13l-6 3l-6 -3l-6 3l0 -13"></path>
                                        <path d="M9 4l0 13"></path>
                                        <path d="M15 7l0 13"></path>
                                    </svg>
                                    <span>{{ $record->place }}</span>
                                </li>
                                @endif

                                @if (EmptyType::stringNotEmpty($record->gps_lat) && EmptyType::stringNotEmpty($record->gps_lon))
                                <li class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                                    </svg>
                                    <span><a href="http://maps.google.com/maps?daddr={{$record->gps_lat}},{{$record->gps_lon}}" target="_blank" class="text-blue-600 underline dark:text-blue-500 hover:no-underline">Google Maps</a></span>
                                    <span><a href="http://mapy.cz/turisticka?x={{$record->gps_lon}}&y={{$record->gps_lat}}&z=14&source=coor&id={{$record->gps_lon}}%2C{{$record->gps_lat}}&q={{$record->gps_lon}}N%20{{$record->gps_lat}}7E" target="_blank" class="text-blue-600 underline dark:text-blue-500 hover:no-underline">Mapy.cz</a></span>
                                </li>
                                @endif
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
                            <p>{{ Markdown::parse($record->entry_desc ?? '') }}</p>
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
