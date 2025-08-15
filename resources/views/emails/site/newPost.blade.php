@php
    use App\Models\Post;

    /** @var Post $post */
@endphp

<x-mail::message>
## Novinka v interní sekci

V členské sekci byla zveřejněna novinka.

----

## {{ $post->title }}

{{ $post->content }}

</x-mail::message>
