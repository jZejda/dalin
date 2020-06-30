{{-- \resources\views\admin\roles\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Stránky')

@section('content')

<!-- Top Content bar -->

@if(Session::has('flash_message'))
  <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
@endif

@if (count($pages) === 0 )

  @component('admin.components.newitem')
    @slot('btnlabel')
      vytvoř stránku
    @endslot
    @slot('btnurl')
      admin/pages/create
  @endslot
  @endcomponent

@elseif (count($pages) >= 1)

<!-- Content top header -->
<div class="adm-main-header">

    <div class="flex justify-between">

        <div class="flex justify-start">
            <h1 class="adm-h1">Stránky</h1>
        </div>
        <div class="flex justify-start">
            <a href="{{ URL::to('admin/pages/create') }}" title="Přidej stránku" class="btn-ico btn-ico-blue">
                <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
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
                    <th>kategorie</th>
                    <th>Odkaz</th>
                    <th style="width: 120px">Akce</th>
                </tr>
            </thead>

            @foreach ($pages as $page)
            <tr class="border-b border-gray-300">
                <td class="pl-2 text-xl">{{ Str::limit($page->title, 60, '...') }}</td>
                <td class="py-2">

                    <div class="flex items-center">
                        @component('app-components.user_avatar')
                            @slot('avatar_bg_color'){{ $page->color }}@endslot
                            @slot('avatar_name'){{ mb_substr($page->name, 0, 2, "UTF-8") }}@endslot
                        @endcomponent
                        {{--
                        <user-avatar-component :name="'{{ mb_substr($page->name, 0, 2, "UTF-8") }}'" :color="'{{ $page->color }}'">
                        </user-avatar-component>
                        --}}
                        <div class="flex flex-col ml-2">
                            <a class="font-bold text-black no-underline" href="#">{{ $page->name }}
                            </a>
                            <span class="text-gray-800 text-sm tracking-tight">{{
                                \Carbon\Carbon::parse($page->created_at)->format('H:i - d.m.Y') }}</span>
                        </div>
                    </div>

                </td>
                <td>
                {{ $category[$page->content_category_id] }}
                </td>
                <td>
                    <div>
                        stranka/{{$page->slug}}
                    </div>
                    <div class="text-gray-700 text-xs">
                        {{ url("/stranka/{$page->slug}") }}
                    </div>

                </td>
                <td class="py-2">

                    <div class="flex">
                        <a href="{{ route('pages.show', $page->id ) }}" class="adm-act-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
                        @can('Edit Page')
                            <a href="{{ route('pages.edit', $page->id ) }}" class="adm-act-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                        @endcan
                        @can('Delete Page')
                            <delete-content-modal-component
                                    :del_content_model="'{{ 'pages' }}'"
                                    :del_content_name="'{{ $page->title }}'"
                                    :del_content_id="'{{ $page->id }}'"
                                    :del_content_czname="'stránku'"
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
