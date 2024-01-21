@php
    use App\Enums\AppRoles;
    use App\Shared\Helpers\AppHelper;
@endphp
<x-filament-panels::page>
    <!-- Icon Blocks -->
    <div class="lg:py-2 max-w-full">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 items-center gap-6">
            <!-- Card User -->
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
                            od {{ \Carbon\Carbon::createFromFormat(AppHelper::MYSQL_DATE_TIME, auth()->user()?->created_at)->format('d.m.Y')}}
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
            <!-- End Card User -->

            <!-- Card Money -->
            <a class="group flex gap-y-6 w-full h-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 rounded-lg p-5 transition-all dark:hover:bg-white/[.075] dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
               href="https://jirizejda.cz/dalin/changelog/" target="_blank">

                <svg class="flex-shrink-0 w-8 h-8 text-gray-800 mt-0.5 me-6 dark:text-gray-200"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>

                @php
                   $fullVersion = \Composer\InstalledVersions::getRootPackage()['version'];
                   $version = explode('.', $fullVersion);
                @endphp
                <div>
                    <div>
                        <h3 class="block font-bold text-gray-800 dark:text-white">Aplikace</h3>
                        <p class="text-gray-600 dark:text-gray-400">Aktuální verze aplikace</p>
                        <p class="mt-4 font-black text-4xl text-gray-600 dark:text-gray-400">{{$version[0]}}.{{$version[1]}}</p>
                    </div>

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
            <!-- End Card Money -->

        </div>
    </div>
    <!-- End Icon Blocks -->

</x-filament-panels::page>
