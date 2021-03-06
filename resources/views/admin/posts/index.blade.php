{{-- \resources\views\admin\roles\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Novinky')

@section('content')

<!-- Top Content bar -->

@if(Session::has('flash_message'))
  <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
@endif

@if (count($posts) === 0 )

  @component('admin.components.newitem')
    @slot('btnlabel')
      vytvoř novinku
    @endslot
    @slot('btnurl')
      admin/posts/create
  @endslot
  @endcomponent

@elseif (count($posts) >= 1)

<!-- Content top header -->
<div class="px-6 py-1 items-center h-12 bg-gray-200 border-b">

    <div class="flex justify-between">

        <div class="flex justify-start">
            <h1 class="adm-h1">Novinky</h1>
        </div>
        <div class="flex justify-start">
            <a href="{{ URL::to('admin/posts/create') }}" title="Přidej novinku" class="btn-ico btn-ico-blue">
                <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </a>
            <a href="https://oplan.cz/prirucka-uzivatele/redaktor.html#novinka" target="_blank" title="Nápověda" class="ml-1 btn-ico btn-ico-yellow">
                <svg class="btn-ico-fater text-gray-700" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12" y2="17"></line></svg>
            </a>

        </div>
    </div>
</div>

<div class="px-4 py-4 mt-8">
    <div class="table-responsive ">

        <table id="standarDatatable" class="table w-full">
            <thead>
                <tr class="bg-gray-200 h-12 text-left">
                    <th class="pl-2">Nadpis</th>
                    <th>Autor</th>
                    <th>Koment.</th>
                    <th>Odkaz</th>
                    <th style="width: 120px">Akce</th>
                </tr>
            </thead>

            @foreach ($posts as $post)
            <tr class="border-b border-gray-300">
                <td class="pl-2 text-xl">{{ Str::limit($post->title, 60, '...') }}

                    @if($post->private == 1)
                    <span class="rounded bg-yellow-600 ml-2 p-2 text-xs inset-y-0 text-white">interní</span></a>
                    @endif
                </td>
                <td class="py-2">

                    <div class="flex items-center">
                        @component('app-components.user_avatar')
                            @slot('avatar_bg_color'){{ $post->color }}@endslot
                            @slot('avatar_name'){{ mb_substr($post->name, 0, 2, "UTF-8") }}@endslot
                        @endcomponent
                        {{--
                        <user-avatar-component :name="'{{ mb_substr($post->name, 0, 2, "UTF-8") }}'" :color="'{{ $post->color }}'">
                        </user-avatar-component>
                        --}}
                        <div class="flex flex-col ml-2">
                            <a class="font-bold text-black no-underline" href="#">{{ $post->name }}
                            </a>
                            <span class="text-gray-700 text-sm tracking-tight">{{
                                \Carbon\Carbon::parse($post->created_at)->format('H:i - d.m.Y') }}</span>
                        </div>
                    </div>

                </td>
                <td>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </td>
                <td>
                    <div>
                        novinka/{{$post->id}}
                    </div>
                    <div class="text-gray-700 text-xs">
                        {{ url("/novinka/{$post->id}") }}
                    </div>

                </td>
                <td class="py-2">
                    <div class="flex">
                        <a href="{{ route('posts.show', $post->id ) }}" class="adm-act-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
                        @can('Edit Post')
                            <a href="{{ route('posts.edit', $post->id ) }}" class="adm-act-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                        @endcan
                        @can('Delete Post')
                            <delete-content-modal-component
                                    :del_content_model="'{{ 'posts' }}'"
                                    :del_content_name="'{{ $post->title }}'"
                                    :del_content_id="'{{ $post->id }}'"
                                    :del_content_czname="'novinku'"
                            >
                            </delete-content-modal-component>
                        @endcan
                    </div>
                </td>
            </tr>

            @endforeach

        </table>

    </div>

</div>


<!-- Errors -->
<div class="px-6">

    @include ('errors.list')


</div>

@else
  <p>zadny vystup</p>
@endif


@endsection
