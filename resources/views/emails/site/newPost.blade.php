@php
    /** @var array{title: string, html: string, text: string} $article */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/new-post.club.eyebrow')"
    :title="__('mail/new-post.club.title')"
    :lead="__('mail/new-post.club.lead')"
    :settings-url="$settingsUrl"
>
<x-mail::club.article :title="$article['title']" :html="$article['html']" :text="$article['text']" />

</x-mail::club.message>
