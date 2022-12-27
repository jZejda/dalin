@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    <p class="text-3xl mt-4 bg-amber-100">This is my body content.</p>

    @php
        $html = Str::markdown('Jirka

**Je borec a kdo nen9**

- fsfsf
- fsdfsf
- fsfsfsf

dfs



fsfs', [
        'html_input' => 'strip',
    ]);


    @endphp
    <div class="flex space-x-2 justify-center">
        <button type="button" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-600 hover:shadow-lg focus:bg-blue-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out flex items-center">
            Notifications
            <span class="inline-block py-1 px-1.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-red-600 text-white rounded ml-2">7</span>
        </button>
    </div>

   <div> {!! $html !!}</div>

    <div class="shadow bg-white">
        <div class="h-16 mx-auto px-5 flex items-center justify-between">
            <a class="text-2xl hover:text-cyan-500 transition-colors cursor-pointer">Logo</a>

            <ul class="flex items-center gap-5">
                <li><a class="hover:text-cyan-500 transition-colors" href="">Link 1</a></li>
                <li><a class="hover:text-cyan-500 transition-colors" href="">Link 2</a></li>
                <li><a class="hover:text-cyan-500 transition-colors" href="">Link 3</a></li>
                <li><a class="hover:text-cyan-500 transition-colors" href="">Link 4</a></li>
                <li><a class="hover:text-cyan-500 transition-colors" href="">Link 5</a></li>
            </ul>
        </div>
    </div>


    <!-- Navbar -->

    <!-- Jumbotron -->
    <div class="text-center bg-gray-50 text-gray-800 py-20 px-6">
        <h1 class="text-5xl font-bold mt-0 mb-6">Heading</h1>
        <h3 class="text-3xl font-bold mb-8">Subeading</h3>
        <a class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out" data-mdb-ripple="true" data-mdb-ripple-color="light" href="#!" role="button">Get started</a>
    </div>
    <!-- Jumbotron -->

@endsection
