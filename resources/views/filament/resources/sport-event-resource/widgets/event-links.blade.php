@php

use App\Models\SportEventLink;

@endphp

<x-filament::widget>
    <x-filament::card>
        <div class="be-widget">

            @php

                $uri = $_SERVER['REQUEST_URI'];
                $uri = explode('/', $uri);

                /** @var SportEventLink $sportEventLink */
                $sportEventLink = SportEventLink::where('sport_event_id', '=', (int)$uri[3])->get();

            @endphp
            @if (count($sportEventLink) > 0)
                <h2 class="mb-2 tracking-tight text-lg font-semibold text-gray-900 dark:text-white">Odkazy</h2>
                @foreach ($sportEventLink as $link)
                    <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                        <li><a href="{{$link->source_url}}" target="_blank">{{!is_null($link->name_cz) ? $link->name_cz : $link->description_cz }}</a></li>
                    </ul>
                @endforeach
            @else
                 <h2 class="mb-2 tracking-tight text-lg font-semibold text-gray-900 dark:text-white">Závod nemá zatím žádné odkazy</h2>
            @endif

        </div>

        {{-- Widget content --}}
    </x-filament::card>
</x-filament::widget>
