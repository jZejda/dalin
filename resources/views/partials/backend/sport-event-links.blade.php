@php

    use App\Models\SportEventLink;

    /**
    * @var SportEventLink[] $sportEventLinks
    */
@endphp
<div>
    @if (count($sportEventLinks) > 0)
        <h2 class="mb-2 tracking-tight text-lg font-semibold text-gray-900 dark:text-white">Odkazy</h2>
        @foreach ($sportEventLinks as $link)
            <ul class="max-w-md space-y-1 text-gray-500 dark:text-gray-400">
                <li class="flex items-center gap-2"><x-link-source-icon :source="$link->source()" /><a href="{{$link->source_url}}" target="_blank">{{ $link->label() }}</a></li>
            </ul>
        @endforeach
    @else
        <h2 class="mb-2 tracking-tight text-lg font-semibold text-gray-900 dark:text-white">Závod nemá zatím žádné odkazy</h2>
    @endif
</div>
