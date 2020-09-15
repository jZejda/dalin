{{-- \resources\views\admin\roles\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Stránka')

@section('content')

<!-- Content top header -->
<div class="adm-main-header">

    <div class="flex justify-between">

        <div class="flex justify-start">
            <h1 class="adm-h1">Stránky</h1>
        </div>
    </div>
</div>


{!! Form::open(array('route' => 'pages.store', 'class' => 'form-horizontal')) !!}
<!-- main content -->
<div class="flex-1 flex flex-row">
    <div class="w-full">
        <div class="px-6 py-4 ">
            <div>
                <!-- Validate Errors -->
                <div class="py-2">
                    @include ('errors.list')
                </div>

                <div class="form-group">
                    {!! Form::label('title', 'Nadpis', array('class' => 'form-label')) !!}
                    {!! Form::text('title', null,
                    array(//'required',
                    'class'=>'form-input-full',
                    'placeholder'=>'nadpis stránky')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('content', 'Obsah', array('class' => 'form-label')) !!}
                    {!! Form::textarea('content', null,
                    ['class'=>'form-input-full overflow-hidden',
                    'id'=>'tinymcEditor',
                    //'id'=>'CKEditor',
                    //'name'=>'CKEditor',
                    'placeholder'=>'obsah stránky ...'])
                    !!}
                </div>
            </div>

            <div class="my-4">
                {!! Form::submit('Ulož stránku', ['class' => 'btn btn-blue']) !!}
                <a href="{{route ('pages.index')}}" class="btn btn-blue-outline">Zpět</a>
            </div>

        </div>
    </div>
    <!-- right content -->
    <div id="content-left-sidebar" class="border-l bg-gray-300 h-screen h-48 w-2/5">
        <div class="px-6 py-4 ">

            <!-- Item iterate -->
            <div class="flex flex-wrap">
                <div class="w-full">
                    {!! Form::label('status', 'Stav', array('class' => 'form-label')) !!}
                    {!! Form::select('status', ['open' => 'ZVEŘEJNĚNO', 'close' => 'NEAKTIVNÍ', 'draft' =>
                    'ROZPRACOVÁNO', 'archiv' => 'ARCHIV'], null, ['class' => 'form-input-full']); !!}
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full">
                    {!! Form::label('category', 'Kategorie', array('class' => 'form-label')) !!}
                    {!! Form::select('content_category_id', $category, null, ['class' => 'form-input-full'] );
                    !!}
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full">
                    {!! Form::label('weight', 'Váha', array('class' => 'form-label')) !!}
                    {!! Form::text('weight', 50,
                    array(//'required',
                    'class'=>'form-input-full',
                    'placeholder'=>'těžší klesá, lehčí stoupá')) !!}
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="w-full">
                    {!! Form::label('user_id', 'Uživatel', array('class' => 'form-label')) !!}
                    {!! Form::select('user_id', $users_editor, Auth::user()->id, ['class' => 'form-input-full'] ); !!}
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="w-full">
                    {!! Form::checkbox('page_menu') !!} Zobrazit menu stránek kategorie?
                </div>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('pageCustomJS')
@include ('admin/components/customjs/default-tinymce')
@endsection
