{{-- \resources\views\users\edit.blade.php --}}

@extends('layouts.admin')

@section('title', '| Upravit uživatele')

@section('content')

    @if(Session::has('flash_message'))
        <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
    @endif

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Uživatel <span class="font-black">{{$user->name}}</span></h1>
            </div>
        </div>
    </div>

    <div class="p-6">

        <div class="max-w-sm rounded overflow-hidden bg-gray-100 shadow-lg">
            <div class="px-6 py-4">
                <div class="font-bold text-2xl mb-1">{{ $user->name }}</div>
                <ul>
                    <li class="text-xl">{{ $user->email }}</li>
                    <li class="p-1 {{ $user->color }} text-white rounded">barva</li>
                    @if($user->avatar == 'default.jpg')

                    @else
                        <li>{{ $user->avatar }}</li>
                    @endif

                </ul>
            </div>
            <div class="px-6 pb-4">
                <p class="my-2 text-gray-800">Tvoje role</p>
                <span class="inline-block bg-gray-300 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">
                    {{ $user->roles()->pluck('name')->implode(' ') }}
                </span>
            </div>
        </div>
        <div class="mt-6">
            @can('Edit member')
                <a href="{{ route('members.edit') }}" class="btn btn-blue">Upravit profil</a>
            @endcan
            @can('Edit member password')
                <a href="{{ route('members.editpassword') }}" class="btn btn-blue-outline">Změnit heslo</a>
            @endcan
        </div>

        @hasrole('Moderator|Super Admin')
        <div class="mt-10">
            <p class="font-black">Moje Novinky</p>
            @if(count($user_posts)>0)
                @foreach($user_posts as $user_post)
                    <ul class="list-disc ml-6">
                        <li><a href="{{ url('admin/posts', [$user_post->id]) }}" class="text-blue-600 hover:text-black hover:underline">{{$user_post->title}}</a></li>
                    </ul>
                @endforeach
            @else
                <p>Zatím jsi žádnou novinu nevytvořil.</p>
            @endif
        </div>
        @endhasrole

    </div>
@endsection
