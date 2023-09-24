@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-900 app-front-content">
        <div class="text-gray-700 dark:text-gray-300">
            <div class="container mx-auto">
                <section class="mx-5 w-24 border-l-8 border-l-green-700">
                    <div class="py-1 text-4xl font-bold">
                        <span class="ml-3">
                            Novinky
                        </span>
                    </div>
                </section>
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
