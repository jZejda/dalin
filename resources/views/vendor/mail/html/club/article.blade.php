@props([
    'title',
    'html',
    'text' => null,
    'separated' => false,
])
{{-- No blank lines: the layout parses its slot as Markdown and a blank line would end this HTML block --}}
<div class="club-article{{ $separated ? ' club-article-separated' : '' }}">
<h2 class="club-heading">{{ $title }}</h2>
{!! $html !!}
</div>
