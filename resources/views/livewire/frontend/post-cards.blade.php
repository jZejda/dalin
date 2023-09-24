@php
    use Carbon\Carbon;
    use App\Models\Post

    /** @var Post $post */
@endphp

<section class="bg-white dark:bg-gray-900 m-5 app-front-content">
    @foreach($posts as $post)
        <article class="format" style="max-width: max-content;">

                <h4 class="card-title">
                    <a href="{{ url('/novinka', $post->id) }}">{{$post->title}}</a>
                </h4>
                @if($post->content_mode === 1 && !is_null($post->editorial))
                    <p>{!! $post->editorial !!}</p>
                @elseif($post->content_mode === 2 && !is_null($post->editorial))
                    <p>{{ Markdown::parse($post->editorial) }}</p>
                @endif

{{--                <p>{!! app(Spatie\LaravelMarkdown\MarkdownRenderer::class)->toHtml($post->content) !!}</p>--}}
                <figcaption class="flex items-center mt-6 space-x-3">
                    <div class="flex items-center divide-x-2 divide-gray-300 dark:divide-gray-700">
                        <cite class="pr-3 font-medium text-gray-900 dark:text-white">{{  $post->user->name }}</cite>
                        <cite class="pl-3 text-sm font-light text-gray-500 dark:text-gray-400">{{ Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->format('H:i - d.h.Y')  }}</cite>
                    </div>
                </figcaption>


        </article>
    @endforeach
    <div class="mb-20"></div>
</section>
