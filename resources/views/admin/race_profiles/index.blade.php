{{-- \resources\views\race_profiles\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Závodní profily')

@section('content')

    @if(Session::has('flash_message'))
        <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
    @endif

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Závodní profily</h1>
            </div>
        </div>
    </div>

    <div class="p-6">


        <div class="max-w-sm rounded overflow-hidden bg-gray-100 shadow-lg">
            <div class="px-6 py-4">
                <div class="font-bold text-2xl mb-1">Jiří Zejda</div>
                <ul>
                    <li>Sport: OB</li>
                    <li>Status: Aktivní</li>
                    <li>Příjmení: Zejda</li>
                    <li>Jméno: Jiří</li>
                    <li>Registrační číslo: ABM8757657</li>
                    <li>Ćíslo čipu</li>
                    <li>Datum narození: 18.5.1978</li>
                    <li>Národnost: Česká republika</li>
                    <li>Adresa: Jírovcova 17</li>
                    <li>Město: Brno</li>
                    <li>PSČ: 62300</li>
                    <li>E-mail: zejda@centrum.cz</li>
                    <li>Mobil: 88768686868</li>
                    <li>Tel. domů: 775756757</li>
                    <li>Pohlaví: H</li>
                    <li>Licence OB: C</li>

                </ul>
            </div>
        </div>

        <div class="mt-6">
            @can('Create RaceProfil')
                <a href="#" class="btn btn-blue">Upravi profil</a>
            @endcan
        </div>

    </div>


@endsection