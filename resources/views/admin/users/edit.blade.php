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


<div class="px-4">


    {{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}{{-- Form model binding to automatically populate our fields with user data --}}

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

    <div class="form-group">
        {!! Form::checkbox('edit-password', 1, null) !!} Chci zároveň nastavit i heslo.
    </div>

    <div class="form-group">
        {!! Form::label('password', 'Heslo', array('class' => 'form-label')) !!}
     {!! Form::password('password',
        array(
        'class'=>'form-input-full',
        'placeholder'=>'silné heslo')) !!}

    {{--<password-field-component></password-field-component>--}}

</div>
<div class="form-group">
    {!! Form::label('password', 'Heslo znovu', array('class' => 'form-label')) !!}
    {!! Form::password('password_confirmation',
    array(//'required',
    'class'=>'form-input-full',
    'placeholder'=>'silné heslo znovu pro kontrolu')) !!}
</div>

@hasanyrole('Super Admin')
<h2>Role</h2>

<div class='form-group my-6'>
    @foreach ($roles as $role)
    {{ Form::checkbox('roles[]',  $role->id, $user->roles ) }}
    {{ Form::label($role->name, ucfirst($role->name)) }}<br>

    @endforeach
</div>
@endhasallroles


{{ Form::submit('Upravit', array('class' => 'btn btn-blue')) }}
<a href="{{route ('users.index')}}" class="btn btn-blue-outline">Zpět</a>

{{ Form::close() }}

</div>


@endsection