{{-- \resources\views\admin\permissions\create.blade.php --}}
@extends('layouts.admin')

@section('title', '| Vytvoř Oprávnění')

@section('content')

<!-- Content top header -->
<div class="adm-main-header">

    <div class="flex justify-between">
        <div class="flex justify-start">
            <h1 class="adm-h1">Vytvoř oprávnění</h1>
        </div>
    </div>
</div>


<div class='px-4'>

    <!-- Validate Errors -->
    <div class="py-2">
        @include ('errors.list')
    </div>

    {{ Form::open(array('url' => 'admin/permissions')) }}

    <div class="form-group">
        {{ Form::label('name', 'Název oprávnění', array('class' => 'form-label')) }}
        {{ Form::text('name', '', array(
            'class' => 'form-input-full',
            'placeholder' => 'nové oprávnění'
            ))
         }}
    </div>
    <br>

    @if(!$roles->isEmpty())

        <p class="form-label">Přiřaď oprávnění konkrétní roli</p>

        @foreach ($roles as $role) 
            {{ Form::checkbox('roles[]',  $role->id ) }}
            {{ Form::label($role->name, ucfirst($role->name)) }}<br>

        @endforeach

    @endif
    
    <br>
    {{ Form::submit('Přidej', array('class' => 'btn btn-blue')) }}
    <a href="{{route ('permissions.index')}}" class="btn btn-blue-outline">Zpět</a>
    {{ Form::close() }}

</div>

@endsection