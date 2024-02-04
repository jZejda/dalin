@extends('layouts.app')

@section('title', 'Novinky')

@section('content')
    <div class="bg-white dark:bg-gray-900 app-front-content">
        <div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
            <div class="container mx-auto">
                <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
                    Novinky
                </div>
            </div>
        </div>
        <div class="container mx-auto">
            <div class="grid md:grid-cols-2  gap-4">
                <div>
                    <section>
                        <livewire:frontend.overview-info />
                    </section>
                </div>
                <div>
                    <section>
                        <livewire:frontend.post-cards />
                    </section>
                </div>
            </div>

        </div>
    </div>
@endsection


