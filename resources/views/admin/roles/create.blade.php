{{-- \resources\views\admin\roles\create.blade.php --}}

@extends('layouts.admin')

@section('title', '| Vytvoř Oprávnění')

@section('content')

<!-- Content top header -->
<div class="adm-main-header">

    <div class="flex justify-between">
        <div class="flex justify-start">
            <h1 class="adm-h1">Vytvoř roli</h1>
        </div>
    </div>
</div>


<div class='px-4'>

    <!-- Validate Errors -->
    <div class="py-2">
        @include ('errors.list')
    </div>

    {{ Form::open(array('url' => 'admin/roles')) }}

    <div class="form-group">
        {{ Form::label('name', 'Název role', array('class' => 'form-label')) }}
        {{ Form::text('name', null, array('class' => 'form-input-full')) }}
    </div>

    <p class="form-label">Přiřaď oprávnění</p>

    <div class='form-group'>
        @foreach ($permissions as $permission)
            {{ Form::checkbox('permissions[]',  $permission->id ) }}
            {{ Form::label($permission->name, ucfirst($permission->name)) }}<br>

        @endforeach
    </div>

    {{ Form::submit('Přidej', array('class' => 'btn btn-blue')) }}
    <a href="{{route ('roles.index')}}" class="btn btn-blue-outline">Zpět</a>
    {{ Form::close() }}

</div>

@endsection