{{-- \resources\views\users\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Uživatelé')

@section('content')

@if(Session::has('flash_message'))
    <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
@endif

<!-- Content top header -->
<div class="adm-main-header">

    <div class="flex justify-between">

        <div class="flex justify-start">
            <h1 class="adm-h1">Uživatelé</h1>
        </div>
        <div class="flex justify-start">
            <a href="{{ route('users.create') }}" title="Nový uživatel" class="btn-ico btn-ico-blue">
                <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
            </a>
            <a href="{{ route('roles.index') }}" title="Role" class="btn-ico btn-ico-blue ml-2">
                <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </a>
            <a href="{{ route('permissions.index') }}" title="Oprávnění" class="btn-ico btn-ico-blue ml-2">
                <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <circle cx="12" cy="12" r="6"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                </svg>
            </a>
        </div>
    </div>
</div>



<div class="px-4 py-4 mt-8">
    <div class="table-responsive ">

        <table id="standarDatatable" class="table w-full">
            <thead>
                <tr class="bg-gray-300 h-12 text-left">
                    <th class="pl-2">Jméno</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="width: 120px">Akce</th>
                </tr>
            </thead>

            @foreach ($users as $user)
            <tr class="border-b border-gray-300">
                <td class="pl-2">
                    <div class="flex items-center">
                        <user-avatar-component :name="'{{ mb_substr($user->name, 0, 2, "UTF-8") }}'"
                            :color="'{{ $user->color }}'">
                        </user-avatar-component>
                        <div class="flex flex-col ml-2">
                            <a class="font-bold text-black no-underline text-xl"
                                href="#">{{ Str::limit($user->name, 60, '...') }}
                            </a>
                            <span class="text-gray-800 text-sm tracking-tight">{{
                                \Carbon\Carbon::parse($user->updated_at)->format('H:i - d.m.Y') }}</span>
                        </div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="adm-role-default">{{ $user->roles()->pluck('name')->implode(' ') }}</span>{{-- Retrieve array of roles associated
                    to a user and convert to string --}}
                </td>
                <td class="py-2 flex">

                    <a href="#" class="adm-act-btn pr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-eye">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </a>
                    <a href="{{ route('users.edit', $user->id) }}" class="adm-act-btn pr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-edit-3">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </a>
                    <delete-content-modal-component
                            :del_content_model="'{{ 'users' }}'"
                            :del_content_name="'{{ $user->name }}'"
                            :del_content_id="'{{ $user->id }}'"
                            :del_content_czname="'uživatele'"
                    >
                    </delete-content-modal-component>
                </td>
            </tr>

            @endforeach

        </table>

    </div>
</div>


@endsection
