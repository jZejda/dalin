{{-- \resources\views\users\edit.blade.php --}}

@extends('layouts.admin')

@section('title', '| Změna hesla')

@section('content')

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Změna hesla uživatele <span class="font-black">{{$user->name}}</span></h1>
            </div>
        </div>
    </div>


    <div class="px-4 max-w-md sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl">

    {{ Form::model($user, array('route' => array('members.updatepassword', $user->id), 'method' => 'PUT')) }}{{-- Form model binding to automatically populate our fields with user data --}}

    <!-- Validate Errors -->
        <div class="py-2">
            @include ('errors.list')
        </div>


    <div class="form-group">
        {!! Form::label('password', 'Heslo', array('class' => 'form-label')) !!}
        {!! Form::password('password',
           array(
           'class'=>'form-input-full',
           'placeholder'=>'silné heslo')) !!}

        {{-- <password-field-component></password-field-component>--}}

    </div>
    <div class="form-group">
        {!! Form::label('password', 'Heslo znovu', array('class' => 'form-label')) !!}
        {!! Form::password('password_confirmation',
        array(//'required',
        'class'=>'form-input-full',
        'placeholder'=>'silné heslo znovu pro kontrolu')) !!}
    </div>


        {{ Form::submit('Změnit heslo', array('class' => 'btn btn-blue')) }}
        <a href="{{route ('members.show', $user->id)}}" class="btn btn-blue-outline">Zpět</a>

        {{ Form::close() }}

    </div>


@endsection
