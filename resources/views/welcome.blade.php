@extends('layouts.app')

@section('title', 'Novinky')

@section('content')
    <div class="bg-white dark:bg-gray-900 app-front-content">
        <div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
            <div class="container mx-auto">
                <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
                    Nadcházející akce
                </div>
            </div>
        </div>
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <section>
                        <div class="z-0 md:mt-8">
                            @livewire(\App\Livewire\Shared\Maps\LeafletMap::class, ['publicMap' => true])
                        </div>
                    </section>
                    <div class="relative flex py-5 items-center">
                        <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                        <span class="flex-shrink mx-4 text-gray-500">Novinky</span>
                        <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                    </div>

                    <section>
                        <livewire:frontend.post-cards />
                    </section>
                </div>
                <div class="md:col-span-1 md:mt-8">
                    <div class="px-4 py-3 sm:px3 lg:px-4 lg:py-0 mx-auto">
                        <!-- End Card -->
                        @livewire(\App\Livewire\Frontend\EventList::class)
                        <!-- End Card -->
                    </div>

                    <div class="px-2 py-2 sm:px3 lg:px-4 lg:py-0 mx-auto">
                        <!-- Centering wrapper -->

                        <div class="relative flex flex-col py-4 md:py-2 bg-[url(https://abmbrno.cz/images/topography2.svg)] bg-yellow-300 text-yellow-500 shadow-md bg-clip-border rounded-xl">
                            <div class="p-6">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cw-12 h-12 mb-4 text-gray-900 dark:text-white"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                                <h5 class="block mb-2 font-sans text-2xl antialiased font-semibold leading-snug tracking-normal text-gray-800 dark:text-gray-800">
                                    12. Jihomoravská liga v orientačním běhu
                                </h5>
                                <p class="text-gray-900 dark:text-black">
                                    Pořádáme závod oblastního žebříčku na klasické trati dne 12.10.2024.
                                </p>
                            </div>
                            <div class="p-6 pt-0">
                                <a href="https://abmbrno.cz/stranka/12-jml-2024-lovcicky" target="_blank" class="inline-block">
                                    <button
                                        class="flex items-center gap-2 px-4 py-2 font-sans text-xs font-bold text-center text-gray-900 dark:text-gray-100 uppercase align-middle transition-all rounded-lg select-none disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none hover:bg-gray-900/50 active:bg-gray-900/10 dark:hover:bg-gray-200/10"
                                        type="button">
                                        Na stránku závodu
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                             stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"></path>
                                        </svg>
                                    </button>
                                </a>
                            </div>
                        </div>

                        <div class="relative flex flex-col mt-6 dark:text-white bg-white dark:bg-gray-800 text-gray-800 border-gray-300 dark:border-gray-700 shadow-md bg-clip-border rounded-xl">
                            <div class="p-6">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     class="w-12 h-12 mb-4 text-gray-900 dark:text-white">
                                    <path fill-rule="evenodd"
                                          d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 019.75 22.5a.75.75 0 01-.75-.75v-4.131A15.838 15.838 0 016.382 15H2.25a.75.75 0 01-.75-.75 6.75 6.75 0 017.815-6.666zM15 6.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"
                                          clip-rule="evenodd"></path>
                                    <path
                                        d="M5.26 17.242a.75.75 0 10-.897-1.203 5.243 5.243 0 00-2.05 5.022.75.75 0 00.625.627 5.243 5.243 0 005.022-2.051.75.75 0 10-1.202-.897 3.744 3.744 0 01-3.008 1.51c0-1.23.592-2.323 1.51-3.008z">
                                    </path>
                                </svg>
                                <h5 class="block mb-2 font-sans text-2xl antialiased font-semibold leading-snug tracking-normal text-blue-gray-900">
                                    dalin
                                </h5>
                                <p class="block font-sans text-base antialiased font-light leading-relaxed text-inherit">
                                    Protože jsou důležitější věci než ručně zajištovat chod klubu. Od toho tu je informační systém, který spoustu věcí řeší za vás.
                                </p>
                            </div>
                            <div class="p-6 pt-0">
                                <a href="https://jirizejda.cz/dalin" target="_blank" class="inline-block">
                                    <button
                                        class="flex items-center gap-2 px-4 py-2 font-sans text-xs font-bold text-center text-gray-900 dark:text-gray-100 uppercase align-middle transition-all rounded-lg select-none disabled:opacity-50 disabled:shadow-none disabled:pointer-events-none hover:bg-gray-900/10 active:bg-gray-900/20 dark:hover:bg-gray-200/10"
                                        type="button">
                                        Zjistit více
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                             stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"></path>
                                        </svg>
                                    </button>
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="relative flex py-5 items-center">
                        <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                        <span class="flex-shrink mx-4 text-gray-500">Partneři</span>
                        <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                    </div>

                    @include('partials.frontend.partner-logos-vertical')

                </div>
            </div>
        </div>
{{--        TODO MAP--}}
{{--        <div class="container mx-auto">--}}
{{--            <div class="grid md:grid-cols-2  gap-4">--}}
{{--                <div>--}}
{{--                    <section>--}}
{{--                        <livewire:frontend.overview-info />--}}
{{--                    </section>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <section>--}}
{{--                        <livewire:frontend.post-cards />--}}
{{--                    </section>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
    </div>
@endsection


