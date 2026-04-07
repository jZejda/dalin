@php
    use App\Enums\AppRoles;
    use App\Models\SportEvent;
    use App\Models\UserEntry;
    use App\Models\UserRaceProfile;
    use Illuminate\Support\Carbon;

    $profileIds = UserRaceProfile::query()
        ->where('user_id', auth()->id())
        ->pluck('id');

    $upcomingEntries = UserEntry::query()
        ->with('sportEvent')
        ->whereIn('user_race_profile_id', $profileIds)
        ->whereHas('sportEvent', fn ($q) => $q->where('date', '>=', Carbon::today())->where('cancelled', false))
        ->join('sport_events', 'user_entries.sport_event_id', '=', 'sport_events.id')
        ->orderBy('sport_events.date')
        ->select('user_entries.*')
        ->limit(2)
        ->get();
@endphp
<x-filament-panels::page>
    <!-- Mapa aktivních eventů -->
    <div class="z-0">
        @livewire(\App\Livewire\Shared\Maps\LeafletMap::class)
    </div>

    <div class="lg:py-2 max-w-full">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 items-stretch gap-6">

            <!-- Card: Uživatel a jeho práva -->
            <a class="group flex gap-y-6 w-full h-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 rounded-lg p-5 transition-all dark:hover:bg-white/[.075] dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
               href="https://jirizejda.cz/dalin/napoveda/role-v-aplikaci.html" target="_blank">
                <svg class="flex-shrink-0 w-8 h-8 text-gray-800 mt-0.5 me-6 dark:text-gray-200"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                </svg>
                <div>
                    <div>
                        <h3 class="block font-bold text-gray-800 dark:text-white">{{ auth()->user()?->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">Registrace
                            od {{ \Carbon\Carbon::createFromFormat(\App\Shared\Helpers\AppHelper::MYSQL_DATE_TIME, auth()->user()?->created_at)->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT)}}
                            na e-mail: {{ auth()->user()?->email }}</p>
                        <p class="mt-4">Aktuální role:</p>
                        <div class="ml-4">
                            @foreach(auth()->user()->getRoleNames() as $role)
                                <div class="inline-flex items-center">
                                    <span class="w-2 h-2 inline-block bg-blue-600 rounded-full me-2 dark:bg-blue-500"></span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ AppRoles::tryFrom($role)->getLabel() }}</span>
                                </div>
                                <br>
                            @endforeach
                        </div>
                    </div>
                    <p class="mt-4 inline-flex items-center gap-x-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Zjistit více
                        <svg class="flex-shrink-0 w-4 h-4 transition ease-in-out group-hover:translate-x-1"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </p>
                </div>
            </a>
            <!-- End Card: Uživatel -->

            <!-- Card: Verze aplikace (OB background) -->
            <a class="group relative flex gap-y-6 w-full h-full rounded-lg p-5 transition-all overflow-hidden
                      bg-amber-50 dark:bg-gray-800
                      hover:bg-amber-100 dark:hover:bg-white/[.075]
                      dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
               href="https://jirizejda.cz/dalin/changelog/" target="_blank">

                {{-- OB decorative background --}}
                <div class="pointer-events-none absolute inset-0 opacity-20 dark:opacity-10" aria-hidden="true">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                        {{-- Vrstevnice (contour lines) --}}
                        <path d="M-20,30 Q40,18 100,28 Q160,38 220,26" stroke="#92640a" stroke-width="1.5" fill="none"/>
                        <path d="M-20,55 Q50,42 110,54 Q170,66 220,52" stroke="#92640a" stroke-width="1.5" fill="none"/>
                        <path d="M-20,80 Q40,68 90,79 Q150,92 220,78" stroke="#92640a" stroke-width="1.5" fill="none"/>
                        <path d="M-20,108 Q60,96 110,107 Q165,118 220,106" stroke="#92640a" stroke-width="1.5" fill="none"/>
                        <path d="M-20,136 Q45,122 95,135 Q155,148 220,132" stroke="#92640a" stroke-width="1.5" fill="none"/>
                        <path d="M-20,162 Q55,150 105,161 Q160,172 220,158" stroke="#92640a" stroke-width="1.5" fill="none"/>
                        {{-- Kontrolní kružnice (control circle) --}}
                        <circle cx="175" cy="75" r="28" stroke="#d9480f" stroke-width="2" fill="none"/>
                        {{-- Křížek středu --}}
                        <line x1="175" y1="64" x2="175" y2="86" stroke="#d9480f" stroke-width="1.5"/>
                        <line x1="163" y1="75" x2="187" y2="75" stroke="#d9480f" stroke-width="1.5"/>
                        {{-- Start trojúhelník --}}
                        <polygon points="30,130 46,158 14,158" stroke="#d9480f" stroke-width="2" fill="none"/>
                    </svg>
                </div>

                {{-- Content --}}
                <svg class="relative flex-shrink-0 w-8 h-8 text-amber-800 mt-0.5 me-6 dark:text-gray-200"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>
                </svg>

                <div class="relative">
                    <h3 class="block font-bold text-gray-800 dark:text-white">Aplikace</h3>
                    <p class="text-gray-600 dark:text-gray-400">Aktuální verze aplikace</p>
                    <p class="mt-4 font-black text-4xl text-amber-800 dark:text-gray-400">12.0</p>
                    <p class="mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Zjistit více
                        <svg class="flex-shrink-0 w-4 h-4 transition ease-in-out group-hover:translate-x-1"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </p>
                </div>
            </a>
            <!-- End Card: Verze aplikace -->

            <!-- Card: Moje nejbližší závody -->
            <div class="flex flex-col w-full h-full bg-gray-100 dark:bg-gray-800 rounded-lg p-5">
                <div class="flex items-start gap-x-4 mb-4">
                    <svg class="flex-shrink-0 w-8 h-8 text-gray-800 mt-0.5 dark:text-gray-200"
                         xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white">Moje nejbližší závody</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Vaše nadcházející přihlášky</p>
                    </div>
                </div>

                @if($upcomingEntries->isEmpty())
                    <div class="flex-1 flex items-center justify-center">
                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center">
                            Žádné nadcházející závody.<br>
                            <a href="{{ route('filament.admin.resources.sport-events.index') }}"
                               class="text-primary-600 dark:text-primary-400 hover:underline font-medium">
                                Přihlásit se na závod →
                            </a>
                        </p>
                    </div>
                @else
                    <ul class="flex-1 space-y-3">
                        @foreach($upcomingEntries as $entry)
                            @php $event = $entry->sportEvent; @endphp
                            @if($event)
                                <li class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700/50 rounded-lg">
                                    <div class="flex-shrink-0 text-center min-w-[2.5rem]">
                                        <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            {{ $event->date?->format('M') }}
                                        </span>
                                        <span class="block text-lg font-black text-gray-800 dark:text-white leading-tight">
                                            {{ $event->date?->format('d') }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                                            {{ $event->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $entry->class_name ?? '—' }}
                                            @if($event->place)
                                                · {{ $event->place }}
                                            @endif
                                        </p>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <a href="{{ route('filament.admin.resources.user-entries.index') }}"
                       class="mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-gray-800 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        Všechny moje přihlášky
                        <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                @endif
            </div>
            <!-- End Card: Moje nejbližší závody -->

        </div>
    </div>

</x-filament-panels::page>
