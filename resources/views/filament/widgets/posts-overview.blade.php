<x-filament::widget>
    <x-filament::card>
        {{-- Widget content --}}
        <div class="text-lg sm:text-xl font-bold tracking-tight">{{$title}}</div>
        {!! \Illuminate\Support\Str::markdown($content) !!}
        {!! $content !!}
    </x-filament::card>
</x-filament::widget>
