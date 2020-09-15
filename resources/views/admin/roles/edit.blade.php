{{-- \resources\views\admin\roles\edit.blade.php --}}

@extends('layouts.admin')

@section('title', '| Uprav Roli')

@section('content')

<!-- Content top header -->
<div class="adm-main-header">

    <div class="flex justify-between">
        <div class="flex justify-start">
            <h1 class="adm-h1">Uprav Roli <span class="font-black">{{$role->name}}</span></h1>
        </div>
    </div>
</div>

<div class="px-4">


    {{ Form::model($role, array('route' => array('roles.update', $role->id), 'method' => 'PUT')) }}

    <!-- Validate Errors -->
    <div class="py-2">
        @include ('errors.list')
    </div>

    <div class="form-group">
        {!! Form::label('name', 'Název Role', array('class' => 'form-label')) !!}
        {!! Form::text('name', null,
        array('class'=>'form-input-full',
        'placeholder'=>'název role')) !!}
    </div>

    <h5><b>Assign Permissions</b></h5>
    @foreach ($permissions as $permission)

        {{Form::checkbox('permissions[]',  $permission->id, $role->permissions ) }}
        {{Form::label($permission->name, ucfirst($permission->name)) }}<br>

    @endforeach
    <br>
    {{ Form::submit('Upravit', array('class' => 'btn btn-blue')) }}
    <a href="{{route ('roles.index')}}" class="btn btn-blue-outline">Zpět</a>

    {{ Form::close() }}    
</div>

@endsection