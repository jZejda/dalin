

<nav class="bg-white border-gray-200 dark:border-gray-600 dark:bg-gray-900">
    <div class="container flex flex-wrap justify-between items-center mx-auto p-4">
        <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
            <svg class="flex-shrink-0 w-11 h-11 text-gray-800 dark:text-gray-200"
                 xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M 8,4 1,23"/>
                <path d="m 16,4 7,19"/>
                <path d="m 8,4 c 0,0 1.0000001,-3 4,-3 2.999999,0 4,3 4,3"/>
                <path d="M 5,23 11,7"/>
                <path d="M 19,23 13,7"/>
                <path d="m 11,7 c 0,0 0.241285,-1 1,-1 0.758714,0 1,1 1,1"/>
                <path style="fill:#ff0000;fill-opacity:1;stroke:#ff0000;" d="m 10,21 2,-7 2,7 z"/>
            </svg>
            <div class="grid grid-cols-1 xl:grid-cols-1">
                <div>
                    <span class="self-center text-2xl font-extrabold tracking-tight whitespace-nowrap dark:text-white">{{ config('site-config.club.abbr') }}</span>
                </div>
                <div>
                    <span class="self-center text-sm tracking-tight whitespace-nowrap dark:text-white">{{ config('site-config.club.full_name') }}</span>
                </div>
            </div>
        </a>
        <button data-collapse-toggle="mega-menu-full" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="mega-menu-full" aria-expanded="false">
            <span class="sr-only">Otevři hlavní menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
            </svg>
        </button>
        <div id="mega-menu-full" class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1">
            <ul class="flex flex-col mt-4 font-medium md:flex-row md:mt-0 md:space-x-8 rtl:space-x-reverse">
                <li>
                    <a href="{{ url('/') }}" class="block py-2 px-3 text-gray-900 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700" aria-current="page">Domů</a>
                </li>
                <li>
                    <button id="mega-menu-full-cta-dropdown-button" data-collapse-toggle="mega-menu-full-cta-dropdown" data-dropdown-placement="bottom" class="flex items-center justify-between w-full py-2 px-3 font-medium text-gray-900 border-b border-gray-100 md:w-auto hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-600 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700">Klub<svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg></button>
                </li>
                <li>
                    <a href="{{ url('/admin') }}" class="block py-2 px-3 text-gray-900 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700">Členská sekce</a>
                </li>
                <li>
                    <button id="theme-toggle" type="button" class="inline-flex items-center p-2 pt-0 m-0 text-sm text-gray-500 rounded-lg focus:outline-none dark:text-gray-400" aria-controls="navbar-default" aria-expanded="false">
                        <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <div id="mega-menu-full-cta-dropdown" class="hidden mt-1 bg-white border-gray-200 shadow-sm border-y dark:bg-gray-800 dark:border-gray-600">
        <div class="grid max-w-screen-xl px-4 py-5 mx-auto text-sm text-gray-500 dark:text-gray-400 md:grid-cols-3 md:px-6">
            <ul class="space-y-4 sm:mb-4 md:mb-0" aria-labelledby="mega-menu-full-cta-button">
                <li>
                    <a href="{{ url('/stranka/o-klubu') }}" class="hover:underline hover:text-blue-600 dark:hover:text-blue-500">
                        Informace o klubu
                    </a>
                </li>
                <li>
                    <a href="https://mapy.orientacnisporty.cz/cs/clubs/abm" class="hover:underline hover:text-blue-600 dark:hover:text-blue-500">
                        Námi spravované mapy
                    </a>
                </li>

            </ul>
            <ul class="space-y-4 sm:mb-4 md:mb-0" aria-labelledby="mega-menu-full-cta-button">
                <li>
                    <a href="{{ url('/stranka/poradane-zavody') }}" class="hover:underline hover:text-blue-600 dark:hover:text-blue-500">
                        Pořádané závody
                    </a>
                </li>
                <li>
                    <a href="{{ url('/stranka/odkazy') }}" class="hover:underline hover:text-blue-600 dark:hover:text-blue-500">
                        Odkazy na orienťácká stránky
                    </a>
                </li>
            </ul>
            <div class="hidden md:block">
                <h2 class="mb-2 font-semibold text-gray-900 dark:text-white">Dalin</h2>
                <p class="mb-2 text-gray-500 dark:text-gray-400">Návod na interní oddílový informační systém.</p>
                <a href="https://jirizejda.cz/dalin/napoveda/" class="inline-flex items-center text-sm font-medium text-blue-600 hover:underline hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-700">
                    Prozkoumat návod
                    <span class="sr-only">Prozkoumat návod </span>
                    <svg class="w-3 h-3 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</nav>
