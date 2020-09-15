<div class="news-block md:flex md:shadow">
    <div class="news-picture lg:h-auto lg:w-64"
         style="background-image: url('/{{ $img_url }}')"
         title="newsImage">
    </div>
    <div class="news-content py-2 px-4 bg-white w-full">
        <div class="mb-8">
            <div class="text-black tracking-tight mb-1">
                <a href="{{ url('/novinka') }}/{{$post_id}}"
                   class="text-black no-underline hover:underline uppercase font-bold text-xl">{{ $post_title }}</a>
                @auth
                    @hasanyrole('Moderator|Super Admin')
                    <span class="text-gray-500 hover:underline"> |
                        <a href="{{ url('admin/posts', [$post_id]) }}/edit">{{ __('edituj') }}</a>
                    </span>
                    @endhasanyrole
                @endauth
            </div>
            <p class="text-gray-800 text-base">{!! $post_editorial !!}</p>
        </div>

        <!-- TODO add link to read-more from title-ID -->
        <div class="flex items-center pb-1">
            @component('app-components.user_avatar')
                @slot('avatar_bg_color'){{ $post_user_color }}@endslot
                @slot('avatar_name'){{ mb_substr($post_user_name, 0, 2, "UTF-8") }}@endslot
            @endcomponent
            {{--
            <user-avatar-component :name="'{{ mb_substr($post_user_name, 0, 2, "UTF-8") }}'"
                                   :color="'{{ $post_user_color }}'">
            </user-avatar-component>
            --}}
            <div class="flex flex-col ml-2">
                <p class="font-bold text-black no-underline">{{ $post_user_name }}</p>
                <span class="text-gray-800 text-sm tracking-tight">{{
                            \Carbon\Carbon::parse($post_created_at)->format('H:i - d.m.Y') }}</span>
            </div>
        </div>
    </div>
</div>
