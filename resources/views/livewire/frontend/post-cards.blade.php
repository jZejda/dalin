<div class="flex flex-wrap justify-center">
    @foreach($posts as $post)
    <div class="card w-96 bg-base-100 shadow-xl">
        <figure><img src="https://api.lorem.space/image/shoes?w=400&h=225" alt="Shoes" /></figure>
        <div class="card-body">
            <h2 class="card-title">
                {{$post->title}}
                <div class="badge badge-secondary">NEW</div>
            </h2>
            <p>{{ $post->editorial }}</p>
            <div class="card-actions justify-end">
                <div class="badge badge-outline">Fashion</div>
                <div class="badge badge-outline">Products</div>
            </div>
        </div>
    </div>
    @endforeach
</div>
