<x-mail::message>
## Byla zveřejněna novinka

Něco nového na stránkách :-).


@foreach ($postContent as $post)
## {{ $post->title }}

{{ $post->content }}

***
@endforeach

</x-mail::message>
