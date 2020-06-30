{{-- \resources\views\users\create.blade.php --}}

@extends('layouts.admin')

@section('title', '| Přidej uživatele')

@section('content')

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">
            <div class="flex justify-start">
                <h1 class="adm-h1">Přidej uživatele</h1>
            </div>
        </div>
    </div>


    <div class='px-4'>

        <!-- Validate Errors -->
        <div class="py-2">
            @include ('errors.list')
        </div>

    {{-- Form::open(array('url' => 'users')) --}}
    {!! Form::open(array('route' => 'users.store', 'class' => 'form-horizontal')) !!}

    <div class="form-group">
        {{ Form::label('name', 'Jméno uživatele', array('class' => 'form-label')) }}
        {{ Form::text('name', '', array('class' => 'form-input-full')) }}
    </div>

    <div class="form-group">
        {{ Form::label('email', 'E-mail', array('class' => 'form-label')) }}
        {{ Form::email('email', '', array('class' => 'form-input-full')) }}
    </div>

    <div class='form-group'>
        @foreach ($roles as $role)
            {{ Form::checkbox('roles[]',  $role->id ) }}
            {{ Form::label($role->name, ucfirst($role->name)) }}<br>

        @endforeach
    </div>

    <div class="form-group">
        {{ Form::label('password', 'Heslo', array('class' => 'form-label')) }}
        {{ Form::password('password', array('class' => 'form-input-full')) }}

    </div>

    <div class="form-group">
        {{ Form::label('password', 'Heslo znovu', array('class' => 'form-label')) }}
        {{ Form::password('password_confirmation', array('class' => 'form-input-full')) }}

    </div>

    {{ Form::submit('Přidej', array('class' => 'btn btn-blue')) }}
    <a href="{{route ('users.index')}}" class="btn btn-blue-outline">Zpět</a>

    {{ Form::close() }}

</div>

@endsection