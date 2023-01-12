<div class="flex flex-wrap justify-center">
    <article class="format lg:format-lg">
        @foreach($posts as $post)
            <div class="card w-96 bg-base-100 shadow-xl">
                <figure><img src="https://api.lorem.space/image/shoes?w=400&h=225" alt="Shoes" /></figure>
                <div class="card-body">
                    <h2 class="card-title">
                        {{$post->title}}
                        <div class="badge badge-secondary">NEW</div>
                    </h2>

                    <p>{{ Markdown::parse($post->content) }}</p>
                    <p>{!! app(Spatie\LaravelMarkdown\MarkdownRenderer::class)->toHtml($post->content) !!}</p>
                    <div class="card-actions justify-end">
                        <div class="badge badge-outline">Fashion</div>
                        <div class="badge badge-outline">Products</div>
                    </div>
                </div>
            </div>
        @endforeach
    </article>

</div>
