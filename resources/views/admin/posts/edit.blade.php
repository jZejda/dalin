{{-- \resources\views\admin\posts\edit.blade.php --}}

@extends('layouts.admin')

@section('title', '| Novinka')

@section('content')

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Upravit novinku</h1>
            </div>
        </div>
    </div>


    {!! Form::model($post, array('method' => 'put', 'route' => ['posts.update', $post->id] )) !!}
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
                            <editorial-field-component :message="'{{ $post->editorial }}'"></editorial-field-component>
                        </div>
                    </div>

                    <div class="flex flex-wrap">
                        <div class="w-full">
                            {!! Form::label('content', 'Obsah', array('class' => 'form-label')) !!}
                            {!! Form::textarea('content', null,
                            ['class'=>'form-input-full',
                            'id'=>'tinymcEditor',
                            //'id'=>'CKEditor',
                            //'name'=>'CKEditor',
                            'placeholder'=>'obsah novinky ...'])
                            !!}
                        </div>
                    </div>

                    <div class="mt-8">
                        {!! Form::submit('Uprav novinku', ['class' => 'btn btn-blue']) !!}
                        <a href="{{route ('posts.index')}}" role="button" class="btn btn-blue-outline">Zpět</a>
                    </div>

                </div>
            </div>
        </div>
        <!-- right content -->
        <div id="content-left-sidebar" class="border-l bg-gray-300 h-screen h-48 w-2/5">
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
                    <!--{!! Form::checkbox('private') !!} Interní novinka v administraci. -->
                </div>

                <div>
                    <div class="inline-flex items-center leading-none">
                        <span class="inline-flex h-4 justify-center items-center">
                            <label class="switch">
                                <input name="private" type="checkbox" {{ $post->private == 1 ? 'checked' : '' }} >
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
