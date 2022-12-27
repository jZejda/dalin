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

    <body class="antialiased">

    @section('sidebar')
        This is the master sidebar.
    @show
        <div class="container">
            @yield('content')
        </div>

        @livewire('notifications')
    </body>
</html>
