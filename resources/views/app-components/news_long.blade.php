<!-- News landing -->
<div class="flex justify-between py-3">
    <div class="tracking-tight text-gray-800 text-center">
        <span class="font-bold text-3xl">{{$post_title}}</span>
        @auth
            @hasanyrole('Moderator|Super Admin')
            <span class="text-gray-500 hover:underline"> |
                        <a href="{{ url('admin/posts', [$post_id]) }}/edit">{{ __('edituj') }}</a>
                    </span>
            @endhasanyrole
        @endauth
    </div>
    <div class="text-gray-800 text-center"></div>
</div>
<!-- End of News landing -->

<div class="flex pb-6">
    <div class="w-full">

        <div class="news-block md:flex">
            <div class="news-picture lg:h-auto lg:w-64"
                 style="background-image: url('/{{ $img_url }}')"
                 title="NewsInfo">
            </div>
            <div class="news-content py-4 px-4 bg-gray-200 w-full">
                <div class="mb-8">
                    <div class="mt-3 text-gray-800 tracking-tight uppercase mb-1">
                        {{ \Carbon\Carbon::parse($post_created_at)->format('H:i - d.m.Y') }}
                    </div>
                    <p class="mt-3 text-gray-800 text-lg">{!! $post_editorial !!}</p>
                </div>
            </div>
        </div>


    </div>

</div>
<div class="app-front-content pb-4 text-lg text-gray-800">
    {!! $post_content  !!}
</div>
