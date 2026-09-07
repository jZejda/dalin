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
            <a class="group flex gap-y-6 w-full h-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 rounded-lg p-5 transition-all dark:hover:bg-white/[.075] dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 ring-1 ring-gray-950/5 dark:ring-gray-400/20 shadow-sm"
               href="https://docs.dalin.cz/napoveda/role-v-aplikaci.html" target="_blank">
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
                    <p class="mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-gray-800 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
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

            <!-- Card: Moje nejbližší závody -->
            <div class="flex flex-col w-full h-full bg-gray-100 dark:bg-gray-800 rounded-lg p-5 ring-1 ring-gray-950/5 dark:ring-gray-400/20 shadow-sm">
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
                                            {{ $event->date?->translatedFormat('M') }}
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
                        <svg class="flex-shrink-0 w-4 h-4 transition ease-in-out group-hover:translate-x-1"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                @endif
            </div>
            <!-- End Card: Moje nejbližší závody -->

            <!-- Card: Verze aplikace (OB background) -->
            <a class="group relative flex gap-y-6 w-full h-full rounded-lg p-5 transition-all overflow-hidden
                      bg-yellow-300
                      before:absolute before:inset-0 before:bg-transparent before:transition-colors hover:before:bg-black/10
                      ring-1 ring-gray-950/5 shadow-sm
                      focus:outline-none focus:ring-2 focus:ring-yellow-500"
               style="background-image: url('{{ asset('images/topography2.svg') }}')"
               href="https://docs.dalin.cz/changelog/" target="_blank">

                {{-- Content: DaLin brand mark (same "d" glyph as the Zudoku docs logo, cropped to its square-ish bounding box) --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 32" class="relative flex-shrink-0 w-8 h-8 text-gray-900 mt-0.5 me-6" aria-hidden="true">
                    <path fill="currentColor" d="m 11.769913,31.74447 c -0.04118,-0.0034 -0.183416,-0.01425 -0.31609,-0.02396 C 8.9994099,31.540777 6.542661,30.502285 4.5277817,28.792809 2.7964055,27.323864 1.3789051,25.1405 0.7314829,22.945405 0.22117563,21.215193 0.14231633,19.251087 0.5090569,17.405507 0.6279655,16.807108 0.86034003,16.022406 1.0857883,15.457942 1.5513684,14.292255 2.3354847,13.012121 3.1995711,12.007019 4.2405946,10.796103 5.5513885,9.7846825 7.0535151,9.0332781 8.7252879,8.197013 10.160476,7.8108104 11.895605,7.7302933 c 1.181381,-0.05482 2.624285,0.1106685 3.704823,0.4249112 0,0 0.09976,0.055379 0.10048,0.1772978 -0.0049,2.2178857 -0.0049,4.4357697 -0.0049,6.6536587 l -0.09617,-0.0653 c -0.497802,-0.338051 -1.232178,-0.672872 -1.844002,-0.840729 -0.957668,-0.262739 -2.0393,-0.248155 -3.027537,0.04082 -0.579414,0.16943 -1.0477114,0.389741 -1.5703661,0.738777 -0.9368697,0.625657 -1.6604001,1.451396 -2.1140508,2.412693 -0.425869,0.902428 -0.586041,1.580396 -0.5856266,2.478814 4.403e-4,0.924524 0.1857466,1.731973 0.5788836,2.522212 0.3897447,0.783422 0.9071504,1.434792 1.5401711,1.938946 0.8285114,0.659848 1.6889738,1.072747 2.6268898,1.260537 0.642349,0.12861 1.388436,0.143702 2.021315,0.04088 1.135898,-0.184533 2.274997,-0.782969 3.145438,-1.652486 1.029643,-1.028549 1.643637,-2.299428 1.752608,-3.627643 0.01224,-0.149111 0.01825,-2.858843 0.01938,-8.725754 9.93e-4,-5.198235 0.0078,-8.5806922 0.01737,-8.6924818 0.04425,-0.5142361 0.179462,-0.8979563 0.471894,-1.3392243 0.350539,-0.52894991 0.82419,-0.94805379 1.339186,-1.1849633 0.834628,-0.38394811 1.786568,-0.38852248 2.575874,-0.0123813 0.345789,0.16478687 0.641082,0.38842712 1.001698,0.7586398 0.475748,0.4884073 0.728881,1.0365155 0.798052,1.7280173 0.01314,0.1313894 0.01668,2.6556287 0.01256,8.9503456 -0.0062,9.49224 -1.26e-4,8.8298 -0.08753,9.499343 -0.121563,0.931143 -0.378068,1.897231 -0.72882,2.744994 -0.371119,0.896988 -0.763317,1.644314 -1.218525,2.321877 -0.896901,1.335008 -2.163489,2.583616 -3.55097,3.500559 -1.720899,1.137291 -3.403359,1.738236 -5.406806,1.931216 -0.257212,0.02477 -1.404779,0.04676 -1.597087,0.0306 z"/>
                    <circle fill="#f57900" cx="12.312178" cy="19.763334" r="3.3639441"/>
                </svg>

                <div class="relative min-w-0">
                    <h3 class="block font-bold text-gray-900">{{ __('dashboard.version.heading') }}</h3>
                    <p class="mt-4 font-black text-2xl text-gray-900 tracking-tight">
                        {{ config('app.name') }} {{ $appVersionTag }}
                    </p>
                    <p class="mt-1 text-xs font-mono text-gray-800">
                        {{ $appVersionBuildLabel }}
                        @if($appVersionDeployedAt)
                            <span aria-hidden="true">&middot;</span>
                            {{ $appVersionDeployedAt }}
                        @endif
                    </p>
                    <p class="text-xs text-gray-800">{{ $appVersionRuntime }}</p>
                    <p class="mt-4 text-gray-800">{{ __('filament/user-setting.changelog_note') }}</p>
                    <p class="mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-gray-900">
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

        </div>
    </div>

</x-filament-panels::page>
