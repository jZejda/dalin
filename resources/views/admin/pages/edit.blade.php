{{-- \resources\views\admin\pages\edit.blade.php --}}

@extends('layouts.admin')

@section('title', '| Stránky')

@section('content')


<!-- Content -->
<div class="flex-1 flex flex-row">

    <div class="w-full">

        <!-- Content top header -->
        <div class="adm-main-header">
            <h1 class="adm-h1">Upravit stránku</h1>
        </div>

    </div>
</div>



{!! Form::model($page, array('method' => 'put', 'route' => ['pages.update', $page->id], 'id' => 'myform')) !!}

<!-- Content -->
<div class="flex-1 flex flex-row">

    <div class="flex-1 flex flex-row">
        <div class="w-full">
            <div class="px-6 py-4 ">

                <!-- Validate Errors -->
                <div class="py-2">
                    @include ('errors.list')
                </div>

                <div>
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
                {!! Form::submit('Upravit stránku', ['class' => 'btn btn-blue']) !!}
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
                    {!! Form::text('weight', null,
                    array(//'required',
                    'class'=>'form-input-full',
                    'placeholder'=>'těžší klesá, lehčí stoupá')) !!}
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="w-full">
                    {!! Form::label('user_id', 'Uživatel', array('class' => 'form-label')) !!}
                    {!! Form::select('user_id', $users_editor, null, ['class' => 'form-input-full'] ); !!}
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="w-full">
                    {!! Form::label('slug', 'Čistá URL', array('class' => 'form-label')) !!}
                    {!! Form::text('slug', null,
                    array(//'required',
                    'class'=>'form-input-full',
                    'placeholder'=>'nazev-oddeleny-pomolckou')) !!}
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

</div>

{!! Form::close() !!}


@endsection

@section('pageCustomJS')

    @include ('admin/components/customjs/default-tinymce')

@endsection
