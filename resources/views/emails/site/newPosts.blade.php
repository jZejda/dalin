@php
    /** @var list<array{title: string, html: string, text: string}> $articles */
    $count = count($articles);
@endphp
<x-mail::club.message
    :eyebrow="__('mail/new-posts.club.eyebrow')"
    :title="trans_choice('mail/new-posts.club.title', $count, ['count' => $count])"
    :lead="__('mail/new-posts.club.lead')"
    :settings-url="$settingsUrl"
>
@foreach ($articles as $article)
<x-mail::club.article :title="$article['title']" :html="$article['html']" :text="$article['text']" :separated="! $loop->first" />

@endforeach
</x-mail::club.message>
