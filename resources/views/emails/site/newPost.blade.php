@php
    use App\Models\Post;

    /** @var Post $post */
@endphp

<x-mail::message>
## {{ __('mail/new-post.body.heading') }}

{{ __('mail/new-post.body.intro') }}

----

## {{ $post->title }}

{{ $post->content }}

</x-mail::message>
