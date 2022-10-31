<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    @livewireStyles
</head>

<body>
 @livewireScripts


 @yield('navbar')

 @yield('content')

 <!-- Footer -->
 @yield('footer')



<script src="{{ mix('js/app.js') }}"></script>
</body>

</html>
