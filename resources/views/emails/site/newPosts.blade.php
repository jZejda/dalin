<x-mail::message>
## Byla zveřejněna novinka

Nejake novinky asi.


@foreach ($postContent as $post)
    ### {{ $post->title }}

    {{ $post->content }}
@endforeach


Mějte se fajn a jezděte na závody - ABM

@component('mail::subcopy')
    Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.
@endcomponent
</x-mail::message>