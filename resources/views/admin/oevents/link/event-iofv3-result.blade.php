{{-- \resources\views\admin\oevents\index-devel.blade.php --}}

@extends('layouts.admin')

@section('title', '| Akce')

@section('content')


    <div class="flex-1 flex flex-row">

        <div class="w-full">
            <div class="px-6 py-1 items-center h-12 bg-gray-200">

                <div class="flex justify-between">

                    <div class="flex justify-start">
                        <h1 class="adm-h1">Výsledky</h1>
                    </div>
                    <div class="flex justify-start">
                        @can('Create Oevents')
                            <a href="{{ URL::to('admin/oevents/create') }}" title="Přidej akci" class="btn-ico btn-ico-blue">
                                <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <event-iofv3-result-component></event-iofv3-result-component>


@endsection
