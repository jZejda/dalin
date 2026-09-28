<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('site-config.club.full_name') }} | @yield('title')</title>
    <x-ui.theme-script />
    <style>[x-cloak] { display: none !important; }</style>
    @filamentStyles
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/css/terrain.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="terrain min-h-screen antialiased">
    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-terrain-accent focus:p-4 focus:text-terrain-on-accent">Přejít na obsah</a>
    <x-ui.navbar />
    <main id="content">
        @yield('content')
        @if (($sponsorSectionId ?? 0) > 0)
            @livewire(\App\Livewire\Frontend\SponsorSection::class, ['sponsorSectionId' => $sponsorSectionId])
        @endif
    </main>
    <x-ui.footer />
    @livewire('notifications')
    @filamentScripts
    @livewireScripts
</body>
</html>
