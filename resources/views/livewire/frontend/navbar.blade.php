<nav class="border-gray-200 px-2 sm:px-4 py-2.5 bg-gray-800">
    <div class="container flex flex-wrap items-center justify-between mx-auto">
        <a href="{{ url('/') }}" class="flex items-center">
{{--            <img src="https://flowbite.com/docs/images/logo.svg" class="h-6 mr-3 sm:h-9" alt="{{ config('app.name') }} Logo" />--}}
            <span class="self-center text-xl font-semibold whitespace-nowrap text-white">{{ config('app.name') }}</span>
        </a>
        <span class="justify-end">
            <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
            </button>
        </span>

        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
            <ul class="flex flex-col p-4 mt-4 md:flex-row md:space-x-8 md:mt-0 text-sm md:text-lg md:font-medium md:border-0">
                <li class="mt-0 md:mt-2">
                    <a href="{{ url('/stranka/odkazy') }}" class="block py-2 pl-3 pr-4 text-gray-300 rounded hover:bg-gray-600 md:hover:bg-transparent md:border-0 md:hover:text-white md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
                        <div class="flex items-center space-x-2">
                            <span>
                               <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-link text-yellow-400" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                               <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                               <path d="M9 15l6 -6"/>
                               <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                               <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/></svg>
                            </span>
                            <span>Odkazy</span>
                        </div>
                    </a>
                </li>
                <li class="mt-0 md:mt-2">
                    <a href="{{ url('/stranka/o-klubu') }}" class="block py-2 pl-3 pr-4 text-gray-300 rounded hover:bg-gray-600 md:hover:bg-transparent md:border-0 md:hover:text-white md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
                        <div class="flex items-center space-x-2">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trees text-yellow-400" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M16 5l3 3l-2 1l4 4l-3 1l4 4h-9"/>
                                    <path d="M15 21l0 -3"/>
                                    <path d="M8 13l-2 -2"/>
                                    <path d="M8 12l2 -2"/>
                                    <path d="M8 21v-13"/>
                                    <path d="M5.824 16a3 3 0 0 1 -2.743 -3.69a3 3 0 0 1 .304 -4.833a3 3 0 0 1 4.615 -3.707a3 3 0 0 1 4.614 3.707a3 3 0 0 1 .305 4.833a3 3 0 0 1 -2.919 3.695h-4z"/>
                                </svg>
                            </span>
                            <span>O klubu</span>
                        </div>
                    </a>
                </li>
                <li class="mt-0 md:mt-2">
                    <a href="{{ url('/admin') }}" class="block py-2 pl-3 pr-4 text-gray-300 rounded hover:bg-gray-600 md:hover:bg-transparent md:border-0 md:hover:text-white md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
                        <div class="flex items-center space-x-2">
                            <span>
                               <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users text-yellow-400" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>
                                  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                                </svg>
                            </span>
                            <span>Přihlásit</span>
                        </div>
                    </a>
                <li>
                    <button id="theme-toggle" type="button" class="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
                        <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    </button>
                </li>
            </ul>
        </div>


    </div>
</nav>
