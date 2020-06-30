{{-- \resources\views\admin\permissions\edit.blade.php --}}
@extends('layouts.admin')

@section('title', '| Uprav oprávnění')

@section('content')

<!-- Content top header -->
<div class="adm-main-header">

    <div class="flex justify-between">
        <div class="flex justify-start">
            <h1 class="adm-h1">Uprav oprávnění <span class="font-black">{{$permission->name}}</span></h1>
        </div>
    </div>
</div>


<div class='px-4'>

    <!-- Validate Errors -->
    <div class="py-2">
        @include ('errors.list')
    </div>

    {{ Form::model($permission, array('route' => array('permissions.update', $permission->id), 'method' => 'PUT')) }}

    <div class="form-group">
        {{ Form::label('name', 'Název oprávnění') }}
        {{ Form::text('name', null, array(
            'class' => 'form-input-full',
            'placeholder' => 'oprávnění ...'))
        }}
    </div>
    <br>
    {{ Form::submit('Edituj', array('class' => 'btn btn-blue')) }}
    <a href="{{route ('permissions.index')}}" class="btn btn-blue-outline">Zpět</a>

    {{ Form::close() }}

</div>

@endsection