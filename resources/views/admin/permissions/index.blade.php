{{-- \resources\views\admin\permissions\index.blade.php --}}
@extends('layouts.admin')

@section('title', '| Dostupná oprávnění')

@section('content')

<!-- Top Content bar -->

@if(Session::has('flash_message'))
    <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
@endif

<!-- Content top header -->
<div class="px-6 py-1 items-center h-12 bg-gray-200 border-b">

    <div class="flex justify-between">

        <div class="flex justify-start">
            <h1 class="adm-h1">Oprávnění</h1>
        </div>
        <div class="flex justify-start">
            <a href="{{ URL::to('admin/permissions/create') }}" title="Přidej oprávnění" class="btn-ico btn-ico-blue">
                <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </a>

        </div>
    </div>
</div>

<div class="px-4 py-4 mt-8">

    <div class="table-responsive">
        <table class="table w-full">

            <thead>
                <tr class="bg-gray-300 h-12 text-left">
                    <th class="pl-2">Oprávnění</th>
                    <th style="width: 120px">Akce</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                <tr class="border-b border-gray-300">
                    <td class="pl-2">{{ $permission->name }}</td>
                    <td>
                        <div class="flex">
                            @can('Manage permissions')
                                <a href="{{ URL::to('admin/permissions/'.$permission->id.'/edit') }}" class="adm-act-btn pr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="feather feather-edit-3">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </a>
                                <delete-content-modal-component
                                        :del_content_model="'{{ 'permissions' }}'"
                                        :del_content_name="'{{ $permission->name }}'"
                                        :del_content_id="'{{ $permission->id }}'"
                                        :del_content_czname="'oprávnění'"
                                >
                                </delete-content-modal-component>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
