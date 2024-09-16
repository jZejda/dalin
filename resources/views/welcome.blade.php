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
                    <section>
                        <livewire:frontend.post-cards />
                    </section>
                </div>
                <div class="md:col-span-1 md:mt-8">
                    <div class="px-4 py-5 sm:px3 lg:px-4 lg:py-0 mx-auto">
                        <!-- End Card -->
                        @livewire(\App\Livewire\Frontend\EventList::class)
                        <!-- End Card -->
                    </div>

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


