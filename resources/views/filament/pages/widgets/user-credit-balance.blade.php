@vite(['resources/css/app.css'])
<x-filament::widget>
    <x-filament::card>
        {{-- Widget content --}}
        <div class="app-front-content">
            <p class="text-gray-500 sm:text-lg dark:text-gray-400">Aktuální stav</p>
            <div class="flex items-baseline my-4">
                <span class="mr-2 text-5xl font-extrabold">{{$user_balance}}</span>
                <span class="text-gray-500 dark:text-gray-400">Kč</span>
            </div>
{{--            @if($user_balance >= 0)--}}
{{--                <p class="text-green-600 sm:text-lg dark:text-green-600">Hurá na závody</p>--}}
{{--            @else--}}
{{--                <p class="text-red-600 sm:text-lg dark:text-red-600">Bylo by dobré zaslat dar</p>--}}
{{--            @endif--}}
        </div>
    </x-filament::card>
</x-filament::widget>
