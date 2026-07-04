@php
    use App\Enums\UserParamType;use App\Models\User;use App\Shared\Helpers\AppHelper;use App\Shared\Helpers\EmptyType;
    use App\Models\SportEvent;
    use App\Models\SportClass;
    use App\Models\SportService;
    use App\Enums\AppRoles;
    use App\Services\OrisApiService;
    use Carbon\Carbon;

    /** @var SportEvent $record */
    /** @var SportClass[] $classes **/
    $classes = SportClass::query()->where('sport_event_id', '=', $record->id)->get();
     /** @var SportEvent[] $services **/
    $services = SportService::query()->where('sport_event_id', '=', $record->id)->get();

    /** @var User $user */
    $user = auth()->user();

    /* @var int $userCreditLimit */
    $userCreditLimit = config('site-config.club.user_credit_limit');

@endphp

{{--<script src="https://cdn.tailwindcss.com"></script>--}}
{{--@vite(['resources/css/app.css'])--}}

<x-filament::page>

    @if( $user->getParam(UserParamType::UserActualBalance) < $userCreditLimit)
        <div
            class="bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500"
            role="alert">
            <span class="font-bold">Upozornění</span> pro nízký stav osobního
            konta {{ $user->getParam(UserParamType::UserActualBalance) }}Kč se aktuálně není možné přihlásit na závody.
        </div>
    @elseif($user->getParam(UserParamType::UserActualBalance) < 0)
        <div
            class="bg-yellow-100 border border-yellow-200 text-sm text-yellow-800 rounded-lg p-4 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500"
            role="alert">
            <span class="font-bold">Upozornění</span> stav osobního konta je
            nízky {{ $user->getParam(UserParamType::UserActualBalance) }}Kč. Při poklesu pod hranici se nebude možné
            přihlásit do závodů.
        </div>

    @endif
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="z-0 md:col-span-2 border border-gray-300 dark:border-gray-700">
            <div>
                @livewire(\App\Livewire\Shared\Maps\LeafletMap::class, ['sportEvent' => $record])
            </div>
        </div>
        <div class="md:col-span-1 p-6 border border-gray-300 rounded-lg dark:border-gray-700 bg-white dark:bg-gray-800">
            @include('partials.backend.sport-event-links', ['sportEventLinks' => $record->sportEventLinks()->get()])
        </div>
    </div>

    <section class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-700">
        <div class="py-4 px-4">
            <div class="flex justify-between content-center">
                <div>
                    <h2 class="mb-2 text-2xl tracking-tight font-extrabold text-gray-900 dark:text-white">{{ $record->name }}
                        @if ($record->cancelled)
                            <span
                                class="align-top bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Zrušeno</span>
                        @endif
                        @if (EmptyType::intNotEmpty($record->oris_id))
                            <span class="font-thin">| {{ $record->oris_id }}</span>
                        @endif
                    </h2>
                </div>
                <div>
                    @if (EmptyType::intNotEmpty($record->oris_id))
                        <a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $record->oris_id }}" target="_blank">
                            <span
                                class="bg-gray-100 text-gray-800 text-sm font-medium mr-1 px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">ORIS</span>
                        </a>
                        <a href="{{ OrisApiService::ORIS_URL }}/Startovka?id={{ $record->oris_id }}" target="_blank">
                            <span
                                class="bg-gray-100 text-gray-800 text-sm font-medium mr-1 px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">P</span>
                        </a>
                        <a href="{{ OrisApiService::ORIS_URL }}/Vysledky?id={{ $record->oris_id }}" target="_blank">
                            <span
                                class="bg-gray-100 text-gray-800 text-sm font-medium mr-1 px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">V</span>
                        </a>
                    @endif
                </div>
            </div>

            <p class="mb-2 text-gray-500 dark:text-gray-400">{{ $record->alt_name }}</p>

            @if (EmptyType::stringNotEmpty($record->event_info))
                <div class="p-2 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                     role="alert">
                    {{ $record->event_info }}
                </div>
            @endif

            @if (EmptyType::stringNotEmpty($record->event_warning))
                <div class="p-2 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
                     role="alert">
                    {{ $record->event_warning }}
                </div>
            @endif

            @if(count($classes) > 0)
                @php
                    /** @var SportClass[] $mensClasses **/
                    $mensClasses = $classes->filter(fn($c) => preg_match('/^H\d{2}[A-Z]?$/i', $c->name));
                    /** @var SportClass[] $womensClasses **/
                    $womensClasses = $classes->filter(fn($c) => preg_match('/^D\d{2}[A-Z]?$/i', $c->name));
                    /** @var SportClass[] $otherClasses **/
                    $otherClasses = $classes->filter(fn($c) => !preg_match('/^[HD]\d{2}[A-Z]?$/i', $c->name));
                @endphp
                <div class="mb-2 ml-1">
                    <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">Kategorie</span>
                    @if($womensClasses->isNotEmpty())
                        <div class="mt-1">
                            @foreach($womensClasses as $class)
                                <span
                                    class="bg-purple-100 text-purple-800 text-sm font-medium px-1 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">{{$class->name}}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($mensClasses->isNotEmpty())
                        <div class="mt-1">
                            @foreach($mensClasses as $class)
                                <span
                                    class="bg-blue-100 text-blue-800 text-sm font-medium px-1 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{$class->name}}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($otherClasses->isNotEmpty())
                        <div class="mt-1">
                            @foreach($otherClasses as $class)
                                <span
                                    class="bg-gray-100 text-gray-800 text-sm font-medium px-1 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{$class->name}}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if(count($services) > 0)
                <div class="mb-2 ml-1">
                    <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">Doplňkové služby</span>
                    @foreach($services as $service)
                        <span
                            class="bg-yellow-100 text-yellow-800 text-sm font-medium px-1 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{{$service->service_name_cz}}</span>
                    @endforeach
                </div>
            @endif

            <div class="grid pt-2 text-left border-t border-gray-200 md:gap-16 dark:border-gray-700 md:grid-cols-3">
                <div>
                    <div class="mb-10">
                        <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                 viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                      clip-rule="evenodd"></path>
                            </svg>
                            Termíny
                        </h3>
                        <div class="px-2">
                            @if ($record->date !== null)
                                <div
                                    class="flex items-center space-x-2 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="icon icon-tabler icon-tabler-calendar-event" width="18" height="18"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                                        <path d="M16 3l0 4"></path>
                                        <path d="M8 3l0 4"></path>
                                        <path d="M4 11l16 0"></path>
                                        <path d="M8 15h2v2h-2z"></path>
                                    </svg>
                                    <span class="text-sm">{{ $record->date->format(AppHelper::DATE_FORMAT) }}</span>
                                </div>
                            @endif

                            @if ($record->entry_date_1 !== null)
                                <li class="flex items-center space-x-2">
                                    <!-- Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="icon icon-tabler icon-tabler-circle-number-1" width="20" height="20"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                        <path d="M10 10l2 -2v8"></path>
                                    </svg>
                                    <span>{{ $record->entry_date_1->format(AppHelper::DATE_TIME_FORMAT) }}</span>
                                </li>
                            @endif
                            @if ($record->entry_date_2 !== null)
                                <li class="flex items-center space-x-2">
                                    <!-- Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="icon icon-tabler icon-tabler-circle-number-2" width="20" height="20"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                        <path
                                            d="M10 8h3a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 0 -1 1v2a1 1 0 0 0 1 1h3"></path>
                                    </svg>
                                    <span>{{ $record->entry_date_2->format(AppHelper::DATE_TIME_FORMAT) }}</span>
                                </li>
                            @endif
                            @if ($record->entry_date_3 !== null)
                                <li class="flex items-center space-x-2">
                                    <!-- Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="icon icon-tabler icon-tabler-circle-number-3" width="20" height="20"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                        <path
                                            d="M10 9a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1"></path>
                                    </svg>
                                    <span>{{ $record->entry_date_3->format(AppHelper::DATE_TIME_FORMAT) }}</span>
                                </li>
                            @endif
                        </div>
                        <h3 class="mt-4 flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                 viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                      clip-rule="evenodd"></path>
                            </svg>
                            Ostatní informace
                        </h3>
                        <div class="text-gray-500 dark:text-gray-400 px-2">


                            <ul class="ml-2">
                                @if (EmptyType::stringNotEmpty($record->place))
                                    <li class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map"
                                             width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5"
                                             stroke="currentColor" fill="none" stroke-linecap="round"
                                             stroke-linejoin="round">
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
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="icon icon-tabler icon-tabler-map-pin" width="20" height="20"
                                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                            <path
                                                d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                                        </svg>
                                        <span><a
                                                href="http://maps.google.com/maps?daddr={{$record->gps_lat}},{{$record->gps_lon}}"
                                                target="_blank"
                                                class="text-blue-600 underline dark:text-blue-500 hover:no-underline">Google Maps</a></span>
                                        <span><a
                                                href="http://mapy.cz/turisticka?x={{$record->gps_lon}}&y={{$record->gps_lat}}&z=14&source=coor&id={{$record->gps_lon}}%2C{{$record->gps_lat}}&q={{$record->gps_lon}}N%20{{$record->gps_lat}}7E"
                                                target="_blank"
                                                class="text-blue-600 underline dark:text-blue-500 hover:no-underline">Mapy.cz</a></span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="mb-10">
                        <div class="app-front-content">

                            @php
                                $allNews = $record->sportEventNews()->orderByDesc('date')->get();
                                $visibleNews = $allNews->take(6);
                                $hiddenNews = $allNews->skip(6);
                            @endphp
                            @if($allNews->isNotEmpty())
                                <h4 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor"
                                         class="mr-2 w-6 h-6 text-gray-500 dark:text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
                                    </svg>
                                    Rychlé novinky
                                </h4>
                                <div x-data="{ expanded: false }">
                                    <dl class="text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
                                        @foreach($visibleNews as $quickNews)
                                            <div class="flex flex-col pb-2">
                                                <dt class="mb-1 text-gray-500 dark:text-gray-400 font-light">{{ Carbon::parse($quickNews->date)->format(AppHelper::DATE_TIME_FORMAT) }}</dt>
                                                <dd class="font-normal">{{ html_entity_decode($quickNews->text) }}</dd>
                                            </div>
                                        @endforeach
                                        @if($hiddenNews->isNotEmpty())
                                            <template x-if="expanded">
                                                <div>
                                                    @foreach($hiddenNews as $quickNews)
                                                        <div class="flex flex-col pb-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                                            <dt class="mb-1 text-gray-500 dark:text-gray-400 font-light">{{ Carbon::parse($quickNews->date)->format(AppHelper::DATE_TIME_FORMAT) }}</dt>
                                                            <dd class="font-normal">{{ html_entity_decode($quickNews->text) }}</dd>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </template>
                                        @endif
                                    </dl>
                                    @if($hiddenNews->isNotEmpty())
                                        <button
                                            type="button"
                                            x-on:click="expanded = !expanded"
                                            class="mt-2 text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                                            <span x-show="!expanded">Zobrazit další novinky ({{ $hiddenNews->count() }})</span>
                                            <span x-show="expanded">Skrýt</span>
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <h4 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400"
                                     fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                Popis akce
                            </h4>
                            <p>{{ Markdown::parse($record->entry_desc ?? '') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <form wire:submit="submit" class="space-y-6">
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

    @php
        $paymentsModuleEnabled = \App\Models\AppSetting::isEventPaymentsModuleEnabled();
        $serviceOrdersEnabled = \App\Models\AppSetting::isServiceOrdersModuleEnabled() && count($services) > 0;
        $transportEnabled = \App\Models\AppSetting::isTransportModuleEnabled()
            && $record->transport_type !== \App\Enums\SportEventTransportType::None;
        $showTabBar = $paymentsModuleEnabled || $serviceOrdersEnabled || $transportEnabled;
    @endphp

    <div x-data="{ tab: 'entries' }" class="space-y-4">
        @if ($showTabBar)
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
            <button
                type="button"
                x-on:click="tab = 'entries'"
                :class="tab === 'entries'
                    ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                class="inline-flex items-center gap-2 px-4 py-2 -mb-px border-b-2 text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Přihlášky
            </button>
            @if ($paymentsModuleEnabled)
            <button
                type="button"
                x-on:click="tab = 'payments'"
                :class="tab === 'payments'
                    ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                class="inline-flex items-center gap-2 px-4 py-2 -mb-px border-b-2 text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>
                Platby / Finance
            </button>
            @endif
            @if ($serviceOrdersEnabled)
            <button
                type="button"
                x-on:click="tab = 'services'"
                :class="tab === 'services'
                    ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                class="inline-flex items-center gap-2 px-4 py-2 -mb-px border-b-2 text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
                Doplňkové služby
            </button>
            @endif
            @if ($transportEnabled)
            <button
                type="button"
                x-on:click="tab = 'transport'"
                :class="tab === 'transport'
                    ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                class="inline-flex items-center gap-2 px-4 py-2 -mb-px border-b-2 text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
                {{ __('transport.page_title') }}
            </button>
            @endif
        </div>
        @endif

        <div x-show="tab === 'entries'" x-cloak>
            @livewire(\App\Livewire\SportEvent\EntryList::class, ['sportEvent' => $record])
        </div>
        @if ($paymentsModuleEnabled)
        <div x-show="tab === 'payments'" x-cloak>
            @livewire(\App\Livewire\SportEvent\RaceProfilePaymentList::class, ['sportEvent' => $record])
        </div>
        @endif
        @if ($serviceOrdersEnabled)
        <div x-show="tab === 'services'" x-cloak class="space-y-4">
            @livewire(\App\Livewire\SportEvent\ServiceList::class, ['sportEvent' => $record])
            @livewire(\App\Livewire\SportEvent\ServiceOrderList::class, ['sportEvent' => $record])
        </div>
        @endif
        @if ($transportEnabled)
        <div x-show="tab === 'transport'" x-cloak>
            @livewire(\App\Livewire\SportEvent\TransportList::class, ['sportEvent' => $record])
        </div>
        @endif
    </div>

</x-filament::page>
