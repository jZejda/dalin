{{-- resources/views/layouts/admin.blade.php --}}
        <!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'oPlan') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @yield('pageCustomCSS')

</head>

<body class="font-sans">
<div id="app" class="font-sans">
    <div class="topnav" id="myTopnav">
        <a href="{{ url('/') }}" class="ml-12 {{ Request::is('/') ? 'active' : '' }}">ABM</a>
        <a href="http://members.eob.cz/abm/" target="_blank">Členská sekce</a>
        <a href="{{ url('stranka/odkazy') }}">Odkazy</a>
        <div class="dropdown">
            <button class="dropbtn">O Klubu
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <a href="{{ url('stranka/alfi-tyden') }}">Alfí týden</a>
                <a href="{{ url('/') }}">Aktuality</a>
                <a href="{{ url('/stranka/o-klubu') }}">O klubu</a>
                <a href="http://members.eob.cz/abm/index.php?id=1" target="_blank">Členové</a>
            </div>
        </div>
        <a href="#about">Akce</a>
        @if(Route::has('login'))
            @auth
                <a href="{{ url('/admin/dashboard') }}">{{ __('Administrace') }}</a>
            @else
                <a href="{{ route('login') }}">{{ __('Přihlásit') }}</a>
            @endauth
        @endif
        <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
    </div>

    <div class="min-h-screen flex flex-col">
        <div class="flex-1 flex flex-row">
            <div class="w-full bg-gray-200">
                @yield('content')
            </div>
        </div>
        <!-- Footer -->
        @include('partials.frontend.footer')

        <div class="hidden md:block bg-gray-900 px-8 py-2">
            <div class="flex justify-center text-gray-300">
                <span>made by <a href="mailto:me@jirizejda.cz?subject=oPlan request" class="text-gray-200">my</a> with &nbsp;</span>

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="feather feather-heart mt-1">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>


                <span>&nbsp; to  <a href="https://vuejs.org/" target="_blank"
                                    class="text-gray-300">vue.js</a>, &nbsp;</span>
                <span><a href="https://laravel.com/" target="_blank" class="text-gray-300">Laravel</a>, &nbsp;</span>
                <span><a href="https://tailwindcss.com" target="_blank"
                         class="text-gray-300">Tailwindcss</a> and &nbsp;</span>
                <a href="https://about.gitlab.com/" target="_blank" class="text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-gitlab mt-1">
                        <path d="M22.65 14.39L12 22.13 1.35 14.39a.84.84 0 0 1-.3-.94l1.22-3.78 2.44-7.51A.42.42 0 0 1 4.82 2a.43.43 0 0 1 .58 0 .42.42 0 0 1 .11.18l2.44 7.49h8.1l2.44-7.51A.42.42 0 0 1 18.6 2a.43.43 0 0 1 .58 0 .42.42 0 0 1 .11.18l2.44 7.51L23 13.45a.84.84 0 0 1-.35.94z"></path>
                    </svg>
                </a>
                <span>&nbsp;| <span><a href="https://oplan.cz/" target="_blank" class="text-gray-300">oPlan</a></span> 2015 - {{ now()->year }}</span>
            </div>
        </div>
        <!-- End of Footer -->
    </div>

</div>


<!-- Scripts -->
<script src="{{ mix('js/app.js') }}"></script>
`<script src="{{ mix('js/app-both.js') }}"></script>`

<script>
    function myFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
</script>

@yield('pageCustomJS')

</body>

</html>
