@extends('layouts.app')

@section('title', 'QR kód – demo')

@section('content')
    <div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
        <div class="container mx-auto">
            <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
                QR kód – demo stylů
            </div>
        </div>
    </div>

    <div class="container mx-auto mb-16 px-5">

        @unless($hasLogo)
            <div class="my-6 p-4 border border-yellow-400 bg-yellow-50 dark:bg-yellow-950 rounded-lg text-yellow-800 dark:text-yellow-200 text-sm">
                <strong>Logo nenalezeno.</strong>
                QR kódy se zobrazují bez vloženého obrázku.
                Nahraj soubor <code class="font-mono bg-yellow-100 dark:bg-yellow-900 px-1 rounded">public/images/qr-logo.png</code>
                a obnov stránku.
            </div>
        @endunless

        <p class="mt-6 mb-8 text-sm text-gray-500 dark:text-gray-400">
            Všechny kódy odkazují na: <code class="font-mono">{{ $url }}</code>
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Varianta 1 --}}
            <div class="flex flex-col items-center gap-3">
                <div class="p-3 bg-white rounded-xl shadow">
                    <img src="data:image/png;base64,{{ $basic }}" alt="QR základní" class="w-48 h-48">
                </div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Základní</h3>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                    Černobílý, čtvercové moduly
                </p>
                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">style('square')</code>
            </div>

            {{-- Varianta 2 --}}
            <div class="flex flex-col items-center gap-3">
                <div class="p-3 bg-white rounded-xl shadow">
                    <img src="data:image/png;base64,{{ $gradient }}" alt="QR gradient" class="w-48 h-48">
                </div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Gradient radial</h3>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                    Tečky, fialová → oranžová
                </p>
                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">style('dot') + gradient radial</code>
            </div>

            {{-- Varianta 3 --}}
            <div class="flex flex-col items-center gap-3">
                <div class="p-3 bg-white rounded-xl shadow">
                    <img src="data:image/png;base64,{{ $rounded }}" alt="QR zaoblené" class="w-48 h-48">
                </div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Modrý zaoblený</h3>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                    Zaoblené moduly, kulaté oči
                </p>
                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">style('round') + eye('circle')</code>
            </div>

            {{-- Varianta 4 --}}
            <div class="flex flex-col items-center gap-3">
                <div class="p-4 bg-slate-900 rounded-xl shadow">
                    <img src="data:image/png;base64,{{ $dark }}" alt="QR tmavý" class="w-48 h-48">
                </div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Tmavý zlatý</h3>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                    Tmavé pozadí, zlatý gradient
                </p>
                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">gradient('diagonal') + dark bg</code>
            </div>

        </div>
    </div>
@endsection
