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
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                <li><a href="{{$link->source_url}}" target="_blank">{{!is_null($link->name_cz) ? $link->name_cz : $link->description_cz }}</a></li>
            </ul>
        @endforeach
    @else
        <h2 class="mb-2 tracking-tight text-lg font-semibold text-gray-900 dark:text-white">Závod nemá zatím žádné odkazy</h2>
    @endif
</div>
