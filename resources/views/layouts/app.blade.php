<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <style>[x-cloak] { display: none !important; }</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @livewireScripts
        @stack('scripts')
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="antialiased bg-white dark:bg-gray-900">
        <div class="container mx-auto">
            <div class="flex flex-col">

                <livewire:frontend.navbar />

                @yield('content')

                <livewire:frontend.footer />


            </div>
        </div>
        @livewire('notifications')
    </body>
    <script src="https://unpkg.com/flowbite@1.6.1/dist/flowbite.min.js"></script>
</html>
