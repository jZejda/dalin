{{-- \resources\views\users\edit.blade.php --}}

@extends('layouts.admin')

@section('title', '| Upravit uživatele')

@section('content')

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Upravit uživatele <span class="font-black">{{$user->name}}</span></h1>
            </div>
        </div>
    </div>


    <div class="px-4 max-w-md sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl">

    {{ Form::model($user, array('route' => array('members.update'), 'method' => 'PUT')) }}{{-- Form model binding to automatically populate our fields with user data --}}

    <!-- Validate Errors -->
        <div class="py-2">
            @include ('errors.list')
        </div>

        <div class="form-group">
            {!! Form::label('name', 'Jméno', array('class' => 'form-label')) !!}
            {!! Form::text('name', null,
            array(//'required',
            'class'=>'form-input-full',
            'placeholder'=>'jméno uživatele, nick ...')) !!}
        </div>

        <div class="form-group">
            {!! Form::label('email', 'E-mail', array('class' => 'form-label')) !!}
            {!! Form::text('email', null,
            array(//'required',
            'class'=>'form-input-full',
            'placeholder'=>'e-mailová adresa')) !!}
        </div>

        <!-- Item iterate -->
        <div class="form-group">
            {!! Form::label('color', 'Moje barva', array('class' => 'form-label')) !!}
            {!! Form::select('color', [
                'avatar-red' => 'červená',
                'avatar-blue' => 'modrá',
                'avatar-green' => 'zelená',
                'avatar-gray' => 'šedá',
                'avatar-yellow' => 'žlutá',
                'avatar-orange' => 'oranžová',
                'avatar-purple' => 'fialová',
                'avatar-pink' => 'růžová',
                'avatar-teal' => 'tyrkysová',
                'avatar-indigo' => 'indigo'
                ], null, ['class' => 'form-input-full']); !!}
        </div>


        {{ Form::submit('Upravit', array('class' => 'btn btn-blue')) }}
        <a href="{{route ('members.show')}}" class="btn btn-blue-outline">Zpět</a>

        {{ Form::close() }}

    </div>


@endsection
