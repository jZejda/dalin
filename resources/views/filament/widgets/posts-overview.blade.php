@php
    use Illuminate\Support\Str;
@endphp
<x-filament::widget>
    <x-filament::card>
        @if($posts->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">Žádné interní novinky.</p>
        @else
            <div
                x-data="{ current: 0, total: {{ $posts->count() }} }"
                class="app-front-content"
            >
                {{-- Posts --}}
                @foreach($posts as $i => $post)
                    <div x-show="current === {{ $i }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-1"
                         x-transition:enter-end="opacity-100 translate-x-0"
                    >
                        <div class="text-lg sm:text-xl font-bold tracking-tight mb-2">{{ $post->title }}</div>
                        @if($post->content_mode === 1)
                            <div class="prose dark:prose-invert max-w-none">{!! $post->content !!}</div>
                        @elseif($post->content_mode === 2)
                            <div class="prose dark:prose-invert max-w-none">{!! Str::markdown($post->content) !!}</div>
                        @endif
                    </div>
                @endforeach

                {{-- Navigation --}}
                @if($posts->count() > 1)
                    <div class="flex items-center gap-3 mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                        {{-- Dots --}}
                        <div class="flex gap-1.5 flex-1 items-center">
                            @foreach($posts as $i => $post)
                                <button
                                    @click="current = {{ $i }}"
                                    :class="current === {{ $i }} ? 'bg-primary-500 w-4' : 'bg-gray-300 dark:bg-gray-600 w-2'"
                                    class="h-2 rounded-full transition-all duration-200"
                                    aria-label="Novinka {{ $i + 1 }}"
                                ></button>
                            @endforeach
                        </div>
                        {{-- Counter --}}
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            <span x-text="current + 1"></span>&nbsp;/&nbsp;{{ $posts->count() }}
                        </span>
                        {{-- Prev / Next --}}
                        <button
                            @click="current = current > 0 ? current - 1 : total - 1"
                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-colors"
                            aria-label="Předchozí novinka"
                        >
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button
                            @click="current = current < total - 1 ? current + 1 : 0"
                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-colors"
                            aria-label="Další novinka"
                        >
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </x-filament::card>
</x-filament::widget>
