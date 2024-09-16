<div class="pt-4 bg-gray-200 dark:bg-gray-800">
    <div class="container mx-auto">
        <footer class="p-4 sm:p-6">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="{{ url('/') }}" class="flex items-center">
{{--                        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 mr-3" alt="FlowBite Logo" />--}}
                        <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">{{ config('app.name') }}</span>
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Odkazy</h2>
                        <ul class="text-gray-600 dark:text-gray-400">
                            <li>
                                <a href="http://zhusta.sky.cz/Jihomoravska_oblast" class="hover:underline">Jihomoravská oblast</a>
                            </li>
                            <li>
                                <a href="http://www.orientacnibeh.cz/" class="hover:underline">Orientační běh</a>
                            </li>
                            <li>
                                <a href="http://www.obpostupy.cz/" class="hover:underline">OB postupy</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Oddíl ABM</h2>
                        <ul class="text-gray-600 dark:text-gray-400">
                            <li>
                                <a href="https://mapy.orientacnisporty.cz/cs/clubs/abm" class="hover:underline ">Naše mapy</a>
                            </li>
                            <li>
                                <a href="{{ url('stranka/poradane-zavody',) }}" class="hover:underline">Poradané závody</a>
                            </li>
                            <li>
                                <a href="#" class="hover:underline">Kalendář</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Pravidla</h2>
                        <ul class="text-gray-600 dark:text-gray-400">
                            <li>
                                <a href="#" class="hover:underline">Zásady</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
            <div class="sm:flex sm:items-center sm:justify-between">
            <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024 <a href="https://abmbrno.cz/" class="hover:underline">{{ config('app.name') }}</a>. Všechna práva vyhrazena. Made with love by <a href="https://github.com/jzejda/dalin" class="hover:underline">dalin</a>
            </span>
                <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
                    <a href=" https://webglobe.cz/?dealer=131151" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 14.5c.23 0 .843-.226 1.487-1.514.306-.612.563-1.37.742-2.236H5.771c.179.866.436 1.624.742 2.236C7.157 14.274 7.77 14.5 8 14.5ZM5.554 9.25a14.444 14.444 0 0 1 0-2.5h4.892a14.452 14.452 0 0 1 0 2.5H5.554Zm6.203 1.5c-.224 1.224-.593 2.308-1.066 3.168a6.525 6.525 0 0 0 3.2-3.168h-2.134Zm2.623-1.5h-2.43a16.019 16.019 0 0 0 0-2.5h2.429a6.533 6.533 0 0 1 0 2.5Zm-10.331 0H1.62a6.533 6.533 0 0 1 0-2.5h2.43a15.994 15.994 0 0 0 0 2.5Zm-1.94 1.5h2.134c.224 1.224.593 2.308 1.066 3.168a6.525 6.525 0 0 1-3.2-3.168Zm3.662-5.5h4.458c-.179-.866-.436-1.624-.742-2.236C8.843 1.726 8.23 1.5 8 1.5c-.23 0-.843.226-1.487 1.514-.306.612-.563 1.37-.742 2.236Zm5.986 0h2.134a6.526 6.526 0 0 0-3.2-3.168c.473.86.842 1.944 1.066 3.168ZM5.31 2.082c-.473.86-.842 1.944-1.066 3.168H2.109a6.525 6.525 0 0 1 3.2-3.168ZM8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0Z" clip-rule="evenodd" /></svg>
                        <span class="sr-only">Webglobe site</span>
                    </a>
                    <a href="https://github.com/jzejda/dalin" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M15.6979 7.28711L8.71226 0.3018C8.31016 -0.100594 7.65768 -0.100594 7.25502 0.3018L5.80457 1.75251L7.64462 3.59255C8.07224 3.44815 8.56264 3.54507 8.90339 3.8859C9.2462 4.22884 9.3423 4.72345 9.19416 5.15263L10.9677 6.92595C11.3969 6.77801 11.8917 6.87364 12.2345 7.21692C12.7133 7.69562 12.7133 8.47162 12.2345 8.95072C11.7553 9.42983 10.9795 9.42983 10.5001 8.95072C10.1399 8.59023 10.0508 8.06101 10.2335 7.61726L8.5793 5.96325V10.3157C8.69594 10.3735 8.80613 10.4505 8.90339 10.5475C9.38223 11.0263 9.38223 11.8022 8.90339 12.2817C8.42456 12.7604 7.64815 12.7604 7.16967 12.2817C6.69083 11.8022 6.69083 11.0263 7.16967 10.5475C7.28802 10.4293 7.42507 10.3399 7.5713 10.28V5.88728C7.42507 5.82742 7.28835 5.73874 7.16967 5.61971C6.80701 5.25717 6.71974 4.72481 6.90575 4.27937L5.09177 2.46512L0.301736 7.25474C-0.100579 7.65747 -0.100579 8.30995 0.301736 8.71233L7.28767 15.6977C7.68985 16.1 8.34213 16.1 8.74491 15.6977L15.6979 8.7447C16.1004 8.34225 16.1004 7.68944 15.6979 7.28711" clip-rule="evenodd" /></svg>
                        <span class="sr-only">GitLab Source</span>
                    </a>
                    <a href="https://discord.gg/b53nY3hQ3W" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5535 3.01557C12.5023 2.5343 11.3925 2.19287 10.2526 2C10.0966 2.27886 9.95547 2.56577 9.82976 2.85952C8.6155 2.67655 7.38067 2.67655 6.16641 2.85952C6.04063 2.5658 5.89949 2.27889 5.74357 2C4.60289 2.1945 3.4924 2.53674 2.44013 3.01809C0.351096 6.10885 -0.215207 9.12284 0.0679444 12.094C1.29133 12.9979 2.66066 13.6854 4.11639 14.1265C4.44417 13.6856 4.73422 13.2179 4.98346 12.7283C4.51007 12.5515 4.05317 12.3334 3.61804 12.0764C3.73256 11.9934 3.84456 11.9078 3.95279 11.8247C5.21891 12.4202 6.60083 12.7289 7.99997 12.7289C9.39912 12.7289 10.781 12.4202 12.0472 11.8247C12.1566 11.9141 12.2686 11.9997 12.3819 12.0764C11.9459 12.3338 11.4882 12.5523 11.014 12.7296C11.2629 13.2189 11.553 13.6862 11.881 14.1265C13.338 13.6871 14.7084 13 15.932 12.0953C16.2642 8.64966 15.3644 5.66336 13.5535 3.01557ZM5.34212 10.2668C4.55307 10.2668 3.90119 9.55072 3.90119 8.6698C3.90119 7.78888 4.53042 7.06653 5.3396 7.06653C6.14879 7.06653 6.79563 7.78888 6.78179 8.6698C6.76795 9.55072 6.14627 10.2668 5.34212 10.2668ZM10.6578 10.2668C9.86752 10.2668 9.21815 9.55072 9.21815 8.6698C9.21815 7.78888 9.84738 7.06653 10.6578 7.06653C11.4683 7.06653 12.1101 7.78888 12.0962 8.6698C12.0824 9.55072 11.462 10.2668 10.6578 10.2668Z" clip-rule="evenodd" /></svg>
                        <span class="sr-only">Discord</span>
                    </a>
                </div>
            </div>
        </footer>
    </div>
</div>
