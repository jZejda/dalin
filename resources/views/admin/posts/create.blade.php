{{-- \resources\views\admin\posts\create.blade.php --}}

@extends('layouts.admin')

@section('title', '| Novinka')

@section('content')

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Vytvoř novinku</h1>
            </div>
        </div>
    </div>


    {!! Form::open(array('route' => 'posts.store', 'class' => 'form-horizontal')) !!}
    <!-- main content -->
    <div class="flex-1 flex flex-row">
        <div class="w-full">
            <div class="px-6 py-4 ">
                <div>
                    <!-- Validate Errors -->
                    <div class="py-2">
                        @include ('errors.list')
                    </div>


                    <div class="flex flex-wrap">
                        <div class="w-full">
                            {!! Form::label('title', 'Nadpis', array('class' => 'form-label')) !!}
                            {!! Form::text('title', null,
                              array(//'required',
                              'class'=>'form-input-full',
                              'placeholder'=>'nadpis novinky')) !!}
                        </div>
                    </div>

                    <!-- TODO make countdown counter https://codepen.io/michmy/pen/JRrEaV -->
                    <div class="flex flex-wrap">
                        <div class="w-full">
                            {!! Form::label('editorial', 'Úvodník - max 220 znaků', array('class' => 'form-label')) !!}
                            {{--
                            {!! Form::textarea('editorial', null,
                              array(
                              'rows' => 2,
                              'class'=>'form-input-full',
                              'placeholder'=>'úvodník novinky')) !!}

                               --}}
                            <editorial-field-component :message="''"></editorial-field-component>
                        </div>
                    </div>

                    <div class="flex flex-wrap">
                        <div class="w-full">
                            {!! Form::label('content', 'Obsah', array('class' => 'form-label')) !!}
                            {!! Form::textarea('content', null,
                              ['class'=>'form-input-full overflow-hidden',
                              'id'=>'tinymcEditor',
                              //'id'=>'CKEditor',
                              //'name'=>'CKEditor',
                              'placeholder'=>'obsah novinky ...'])
                            !!}
                        </div>
                    </div>

                    {!! Form::hidden('user_id', Auth::user()->id ) !!} {{-- usrID: {{ Auth::user()->id }} --}}

                    <br>
                    <div>

                        {!! Form::submit('Ulož novinku', ['class' => 'btn btn-blue']) !!}
                        <a href="{{route ('posts.index')}}" class="btn btn-blue-outline">Zpět</a>

                    </div>



                </div>

            </div>
        </div>
        <!-- right content -->
        <div id="content-left-sidebar" class="border-l bg-gray-200 h-screen h-48 w-2/5">
            <div class="px-6 py-4 ">

                <!-- Item iterate -->
                <div class="flex flex-wrap">
                    <div class="w-full">
                        {!! Form::label('img_url', 'URL obrázku', array('class' => 'form-label')) !!}
                        {!! Form::text('img_url', null,
                          array(
                          'class'=>'form-input-full',
                          'placeholder'=>'URL obrázku')) !!}
                    </div>
                    <p class="text-gray-500 text-sm">např: <strong>media/2019/05/thubnails/obrazek.jpg</strong></p>
                </div>

                <div class="my-4 mt-6">
                    <div class="inline-flex items-center leading-none">
                        <span class="inline-flex h-4 justify-center items-center">
                            <label class="switch">
                                <input name="private" type="checkbox">
                                <span class="slider round"></span>
                            </label>
                        </span>
                        <span class="inline-flex px-2">Interní novinka v administraci</span>
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
