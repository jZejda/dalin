@php
    use Carbon\Carbon;
    use App\Models\Post;
    use App\Enums\ContentFormat;

    /** @var Post $post */
@endphp

<section class="bg-white dark:bg-gray-900 m-5">
    @foreach($posts as $post)
        <article class="mb-8" style="max-width: max-content;">

                <h4 class="card-title">
                    <a href="{{ url('/novinka', $post->id) }}">{{$post->title}}</a>
                </h4>
                @if($post->content_mode === ContentFormat::Html && !is_null($post->editorial))
                    <p>{!! $post->editorial !!}</p>
                @elseif($post->content_mode === ContentFormat::Markdown && !is_null($post->editorial))
                    <p>{{ Markdown::parse($post->editorial) }}</p>
                @endif

{{--                <p>{!! app(Spatie\LaravelMarkdown\MarkdownRenderer::class)->toHtml($post->content) !!}</p>--}}

            <div class="pb-3 flex items-center text-sm text-gray-800 after:flex-1 after:border-t after:border-gray-200 after:ms-6 dark:text-white dark:after:border-neutral-600">

                <figcaption class="flex items-center space-x-2">
                    <div class="flex items-center divide-x-2 divide-gray-300 dark:divide-gray-700">
                        <cite class="pr-3 font-medium text-gray-900 dark:text-white">{{  $post->user->name }}</cite>
                        <cite class="pl-3 text-sm font-light text-gray-500 dark:text-gray-400">{{ Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->format('H:i - d.h.Y')  }}</cite>
                    </div>
                </figcaption>
            </div>
        </article>

    @endforeach
    <div class="mb-20"></div>
</section>
