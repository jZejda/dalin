<x-mail::message>
## {{ __('mail/new-posts.body.heading') }}

{{ __('mail/new-posts.body.intro') }}


@foreach ($postContent as $post)
## {{ $post->title }}

{{ $post->content }}

***
@endforeach

</x-mail::message>
