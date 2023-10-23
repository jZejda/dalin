@extends('layouts.app')

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
            <section>
{{--                content--}}
            </section>
            <livewire:frontend.post-cards />
        </div>
    </div>
@endsection
