@php
    use App\Models\SportEvent;
    use Carbon\Carbon;
    use App\Models\Post;
    use App\Enums\ContentFormat;

    /** @var SportEvent[] $events */
    /** @var string $ourClub */
@endphp

<div class="terxt-white dark:text-white">
    @foreach($events as $event)
        @foreach($event->organization as $organization)
            @if($organization === 'ABM')
                @php($ourClub = 'border-yellow-300 dark:border-yellow-300')
            @else
                @php($ourClub = 'border-gray-300 dark:border-gray-700')
            @endif
        @endforeach
        <div class="flex border-2 {{$ourClub}} shadow-sm rounded-xl mb-2 hover:shadow-md focus:outline-none focus:shadow-md transition">

            <!-- Left Section -->
            <div class="w-1/5 p-2 border-r-2 border-gray-300 dark:border-gray-700 border-dotted">
                <div class="text-center text-2xl font-black">{{$event->date->format('d')}}/{{$event->date->format('m')}}</div>
                <div class="text-center text-md tracking-tighter text-gray-600 dark:text-gray-400">
                    <span class="font-bold">
                        @if($event->date->format('N') === 1)
                            Po
                        @elseif($event->date->format('N') === '2')
                            Út
                        @elseif($event->date->format('N') === '3')
                            Středa
                        @elseif($event->date->format('N') === '4')
                            Čtvrtek
                        @elseif($event->date->format('N') === '5')
                            Pátek
                        @elseif($event->date->format('N') === '6')
                            So
                        @elseif($event->date->format('N') === '7')
                            Ne
                        @endif
                    </span>
                    <span>
                       {{$event->date->format('Y')}}
                    </span>
                </div>
                <div class="font-bold text-center text-md tracking-tighter text-gray-600 dark:text-gray-400">
                    @if($event->event_type->value === 'race')
                        Závod
                    @elseif($event->event_type->value === 'race')
                        Trénink
                    @elseif($event->event_type->value === 'race')
                        Soustředění
                    @elseif($event->event_type->value === 'race')
                        Ostatní
                    @endif
                </div>

            </div>
            <!-- Right Section -->
            <div class="w-4/5 p-2">
                <div class="tracking-tight font-black">{{$event->name}}</div>
                <div class="tracking-tight text-gray-600 dark:text-gray-400">{{$event->alt_name}}</div>
                <div class="tracking-tight">

                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="icon icon-tabler icon-tabler-map-pin" width="20" height="20"
                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                <path
                                    d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                            </svg>
                            <div>
                                {{Str::limit($event->gps_lat, 5, '')}}N, {{Str::limit($event->gps_lon, 5, '')}}E
                            </div>
                        </div>

                        @if(\App\Shared\Helpers\EmptyType::stringNotEmpty($event->place))
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map"
                                     width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor" fill="none" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M3 7l6 -3l6 3l6 -3l0 13l-6 3l-6 -3l-6 3l0 -13"></path>
                                    <path d="M9 4l0 13"></path>
                                    <path d="M15 7l0 13"></path>
                                </svg>

                                <div>
                                    {{Str::limit($event->place, 15)}}
                                </div>
                            </div>
                        @else
                            <div></div>
                        @endif

                    </div>
                </div>
                <div class="tracking-tight">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="20" height="20" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        @if($event->oris_id !== null)
                            <a
                                class="ml-1 inline-flex items-center gap-x-1.5 py-1 px-2 rounded-md text-xs font-medium border border-gray-300 bg-white text-gray-800 shadow-sm dark:bg-neutral-900 dark:border-700 dark:text-white no-underline" style="text-decoration: none !important;"
                                href='https://oris.orientacnisporty.cz/Zavod?id={{$event->oris_id}}'
                                target='_blank';
                            >
                                {{ $event->oris_id }}
                            </a>
                        @endif
                        @if((count($event->organization ?? []) > 0))
                            <div class="ml-1 inline-flex items-center gap-x-1.5 py-1 px-2 rounded-md text-xs font-medium border border-gray-300 bg-white text-gray-800 shadow-sm dark:bg-neutral-900 dark:border-700 dark:text-white">
                                {{ ((count($event->organization ?? []) > 0) ? Arr::join($event->organization, ', ') : '') }}
                            </div>
                        @endif
                        @if((count($event->region ?? []) > 0))
                            <div class="ml-1 inline-flex items-center gap-x-1.5 py-1 px-2 rounded-md text-xs font-medium border border-gray-300 bg-white text-gray-800 shadow-sm dark:bg-neutral-900 dark:border-700 dark:text-white">
                                {{ ((count($event->region ?? []) > 0) ? Arr::join($event->region, ', ') : '') }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endforeach

</div>

