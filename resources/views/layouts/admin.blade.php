{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'oPlan') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @yield('pageCustomCSS')
</head>
<body class="font-sans">
    <div class="font-sans antialiased h-screen flex" id="app">
        <!-- Sidebar -->
        <div class="bg-blue-700 flex-none text-white w-64 hidden md:block">
            <div id="logo" class="flex h-12 items-center px-4 py-2">
                <div class="flex-1 text-xl font-black">
                    <a href="{{ URL('/') }}" target="_blank">{{ config('app.name', 'oPLAN') }}</a>
                </div>
            </div>
            <ul id="menu" class="flex flex-col list-reset">
                <!-- user section -->
                @guest
                   <a class="no-underline hover:underline text-teal-700 pr-3 text-sm" href="{{ url('/login') }}">{{ __('Login') }}</a>
                   <a class="no-underline hover:underline text-teal-700 text-sm" href="{{ url('/register') }}">{{ __('Register') }}</a>
                @else
                <div class="bg-blue-900 px-3 flex items-center hover:bg-gray-800 cursor-pointer">
                    <div>
                        @component('app-components.user_avatar')
                            @slot('avatar_bg_color'){{ Auth::user()->color }}@endslot
                            @slot('avatar_name'){{ mb_substr(Auth::user()->name, 0, 2, "UTF-8") }}@endslot
                        @endcomponent
                        {{--
                        <user-avatar-component :name="'{{ mb_substr(Auth::user()->name, 0, 2, "UTF-8") }}'" :color="'{{ Auth::user()->color }}'">
                        </user-avatar-component>
                        --}}
                    </div>
                    <div class="ml-2 flex-1 py-4">
                        <div class="flex items-bottom justify-between">
                            <a href="{{ url('/admin/members')}}" class="text-white hover:underline">
                                {{ Auth::user()->name }}
                            </a>
                            <p class="text-xs text-gray-200">
                                {{-- TODO some login info --}}
                            </p>
                        </div>
                        <p class="text-gray-200 mt-1 text-xs">
                            {{ Auth::user()->email }}
                        </p>
                        <p class="pt-2">
                        <a href="{{ route('logout') }}"
                                class="no-underline hover:underline text-gray-200 text-sm"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">{{ __('Odhlásit se') }}</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                {{ csrf_field() }}
                            </form>
                        </p>
                    </div>
                </div>
                @endguest
                <li class="block">
                    <a href="{{ url('admin/dashboard') }}" class="flex-1 flex items-center hover:bg-blue-800 no-underline block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500 {{ Request::is('admin/dashboard*') ? 'text-white bg-blue-800 border-red-500' : 'text-gray border-blue-700' }} block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home mr-2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Úvod
                    </a>
                </li>
                @hasanyrole('Super Admin')
                <li class="block">
                    <a href="{{ url('admin/oevents/list') }}/{{ \Carbon\Carbon::now()->year }}/now" class="flex-1 flex items-center hover:bg-blue-800 no-underline {{ Request::is('admin/oevents*') ? 'text-white bg-blue-800 border-red-500' : 'text-gray border-blue-700' }} block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin mr-2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Akce
                    </a>
                </li>
                @endhasallroles
                @hasanyrole('Moderator|Super Admin')

                <li class="block">
                    <a href="{{ url('admin/posts') }}" class="flex-1 flex items-center hover:bg-blue-800 no-underline block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500 {{ Request::is('admin/posts*') ? 'text-white bg-blue-800 border-red-500' : 'text-gray border-blue-700' }}">
                    <svg class="fill-current text-gray inline-block mr-2" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20"><path d="M16 2h4v15a3 3 0 0 1-3 3H3a3 3 0 0 1-3-3V0h16v2zm0 2v13a1 1 0 0 0 1 1 1 1 0 0 0 1-1V4h-2zM2 2v15a1 1 0 0 0 1 1h11.17a2.98 2.98 0 0 1-.17-1V2H2zm2 8h8v2H4v-2zm0 4h8v2H4v-2zM4 4h8v4H4V4z"/></svg>

                        Novinky
                    </a>
                </li>
                <li class="block">
                    <a href="{{ url('admin/pages') }}" class="flex-1 flex items-center hover:bg-blue-800 no-underline {{ Request::is('admin/pages*') ? 'text-white bg-blue-800 border-red-500' : 'text-gray border-blue-700' }} block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book mr-2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        Stránky
                    </a>
                </li>
                <li class="block">
                    <a href="{{ url('admin/manage-category') }}" class="flex-1 flex items-center hover:bg-blue-800 no-underline {{ Request::is('admin/manage-category*') ? 'text-white bg-blue-800 border-red-500' : 'text-gray border-blue-700' }} block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers mr-2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        Kategorie
                    </a>
                </li>
                <li class="block">
                    <a href="{{ url('admin/media') }}/{{ \Carbon\Carbon::now()->year }}/files" class="flex-1 flex items-center hover:bg-blue-800 no-underline {{ Request::is('admin/media*') ? 'text-white bg-blue-800 border-red-500' : 'text-gray border-blue-700' }} block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Media
                    </a>
                </li>
                @endhasallroles
                @hasanyrole('Super Admin')
                <li class="block">
                    <a href="{{ url('admin/users') }}" class="flex-1 flex items-center hover:bg-blue-800 no-underline {{ Request::is('admin/users*') ? 'text-white bg-blue-800 border-red-500' : 'text-gray border-blue-700' }} block h-full w-full px-3 py-3 hover:text-white border-l-4 hover:border-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users mr-2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        Uživatelé
                    </a>
                </li>
                @endhasallroles
            </ul>
        </div><!-- End of Sidebar -->
        <!-- Second Sidebar
        <div class="bg-gray-dark flex-none w-48 pb-6 hidden md:block">
        </div>
        End of Second Sidebar -->
        <!-- Main -->
        <div class="flex-1 flex flex-col  overflow-y-scroll">
            <!-- Top bar -->
            <!--
            <div class="border-b flex px-6 py-2 items-center flex-none h-12">
                <p>Search</p>
            </div>
        -->
            <!-- Content -->
            <div class="flex-1 flex flex-row">
                <div class="w-full">
                    @yield('content')
                </div>
            </div>
        </div><!-- End of Main -->
    </div>

    <!-- Scripts -->
    <script src="{{ mix('js/app-backend.js') }}"></script>

    @yield('pageCustomJS')

</body>
</html>
