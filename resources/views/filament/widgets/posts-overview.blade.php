<x-filament::widget>
    <x-filament::card>
        {{-- Widget content --}}
        <div class="app-front-content">
            <div class="text-lg sm:text-xl font-bold tracking-tight">{{$title}}</div>
            @if($content_mode === 1)
                <span>{!! $content !!}</span>
            @elseif($content_mode === 2)
                <span>{!! \Illuminate\Support\Str::markdown($content) !!}</span>
            @endif

        </div>
    </x-filament::card>
</x-filament::widget>
