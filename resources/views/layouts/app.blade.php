<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} | @yield('title')</title>

        <style>[x-cloak] { display: none !important; }</style>
        @filamentStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')

        <script>
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>

    <body class="antialiased bg-white dark:bg-gray-900">
        <div class="flex flex-col h-screen">
            <header>
                <livewire:frontend.navbar />
            </header>
            <main>
                @yield('content')

                @if ($sponsorSectionId > 0)
                    @livewire(\App\Livewire\Frontend\SponsorSection::class, ['sponsorSectionId' => $sponsorSectionId])
{{--                    <livewire:frontend.sponsor-section />--}}
                @endif
            </main>
            <footer class="h-10 bg-blue-500">
                <livewire:frontend.footer />
            </footer>
            @livewire('notifications')
        </div>
        @filamentScripts
        @vite('resources/js/app.js')
    </body>

{{--    <script src="https://unpkg.com/flowbite@1.6.1/dist/flowbite.min.js"></script>--}}
    <script src="./node_modules/preline/dist/preline.js"></script>

    <script>
        function initThemeToggle() {
            var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
            var themeToggleBtn = document.getElementById('theme-toggle');

            // Check if elements exist
            if (!themeToggleBtn || !themeToggleDarkIcon || !themeToggleLightIcon) {
                return false;
            }

            // Change the icons inside the button based on previous settings
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                themeToggleLightIcon.classList.remove('hidden');
                themeToggleDarkIcon.classList.add('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
                themeToggleLightIcon.classList.add('hidden');
            }

            // Remove existing event listeners by cloning the button
            var newThemeToggleBtn = themeToggleBtn.cloneNode(true);
            themeToggleBtn.parentNode.replaceChild(newThemeToggleBtn, themeToggleBtn);

            // Add click event listener
            newThemeToggleBtn.addEventListener('click', function() {
                var darkIcon = document.getElementById('theme-toggle-dark-icon');
                var lightIcon = document.getElementById('theme-toggle-light-icon');
                var htmlElement = document.documentElement;

                // Toggle dark mode class on html element
                var isDark = htmlElement.classList.contains('dark');

                if (isDark) {
                    htmlElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                    darkIcon.classList.remove('hidden');
                    lightIcon.classList.add('hidden');
                } else {
                    htmlElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                    darkIcon.classList.add('hidden');
                    lightIcon.classList.remove('hidden');
                }
            });

            return true;
        }

        // Initialize on DOM content loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Try to initialize immediately
            if (!initThemeToggle()) {
                // If elements don't exist yet, try again after a short delay
                setTimeout(function() {
                    initThemeToggle();
                }, 100);
            }
        });

        // Also initialize when Livewire finishes loading/updating
        document.addEventListener('livewire:init', function() {
            setTimeout(initThemeToggle, 50);
        });

        document.addEventListener('livewire:navigated', function() {
            setTimeout(initThemeToggle, 50);
        });
    </script>
</html>
