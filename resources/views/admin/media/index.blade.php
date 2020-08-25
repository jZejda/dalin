{{-- \resources\views\admin\media\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Média')

@section('content')

<!-- Top Content bar -->

@if(Session::has('flash_message'))
<toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'">
</toasted-component>
@endif

<div>

    <div class="px-6 py-1 items-center bg-blue-500 py-4">


        <div class="flex justify-start">
            <h2 class="adm-h1 text-white">Nahraj nový soubor</h2>
        </div>
        <p class="font-light text-blue-100 py-1">Nejdříve vyber soubor (Najdi soubor ...), následně klikni na Uložit
            soubor. Soubor se automaticky uloží do adresáře aktuálního roku a měsíce.</p>
        <p class="font-light text-blue-100">Formulář očekává pouze koncovky jpeg,png,gif,zip,pdf,doc,docx,xls,xlsx. Nepoužívejte
            diaktritiku v názvech souborů.</p>
        <p class="font-light text-blue-100">Soubor nesmí být větší jak 1,5 MB. Pokud se jedná o velký obrázek, změnši jeho rozlišení pod uvedeou velikost.</p>

        @if(auth()->user()->can('Add File'))
        {!! Form::open(
        array(
        'route' => 'admin.media.store',
        'class' => 'form',
        'novalidate' => 'novalidate',
        'files' => true)) !!}

        <div class="my-4">
            <input type="file" class="form-control-file text-white" name="file" id="file" aria-describedby="fileHelp">
            <span class="font-light text-blue-100">Prosím nahraj soubor s velikosti do koncovkou uvedenou
                výše.</span>
        </div>
        <button type="submit"
            class="bg-transparent hover:bg-blue-700 text-blue-100 no-underline font-light hover:text-white py-2 px-4 my-2 border border-blue-200 rounded">Ulož
            soubor</button>
        {!! Form::close() !!}
        @else
        <div class="bg-blue-600 border-t border-b border-blue-400 text-white px-4 py-3 mt-4" role="alert">
            <p class="font-bold">Pozor</p>
            <p class="text-sm">Pro přidání novéhou souborů nemáte dostatečné oprávnění. Pokud je to chyba, kontaktuj
                správce.</p>
        </div>
        @endif

    </div>
    <!-- Content top header -->
    <div class="px-6 py-1 items-center bg-gray-200 border-b pb-4">

        <div class="flex mt-6 mb-2">
            <div class="flex justify-start">
                @foreach( array_reverse($yearsDir) as $yearDir)
                <span class="pull-right">
                    <a class="btn btn-blue mr-1" href="{{ URL('admin/media')}}/{{ $yearDir }}/files" role="button">
                        {{ $yearDir }}
                    </a>
                </span>
                @endforeach

            </div>
        </div>
    </div>

    <!-- Errors -->
    <div class="px-6">

        @include ('errors.list')

    </div>

    <div class="px-4 py-4">



        {{-- https://stackoverflow.com/questions/39239049/upload-file-in-laravel --}}


        <h1 class="adm-h1">{{ $year }}</h1>
        <p class="mb-4">Seznam souborů v daném roce. </p>

        <div class="table-responsive ">

            <table id="standarDatatable" class="table w-full">
                <thead>
                    <tr class="bg-gray-200 h-12 text-left">
                        <th>Soubor</th>
                        <th>URL</th>
                        <th style="width: 100px">Akce</th>
                    </tr>
                </thead>

                @foreach( array_reverse($files) as $file)
                <tr class="border-b h-12">

                    @if (strpos($file, 'thubnails') !== false)
                        <td class="text-gray-500">{{ $file }}
                            <span class="p-1 border border-gray-700 border-dashed rounded text-gray-700" >zmenšenina</span>
                        </td>
                        <td class="text-gray-500"><a href="{{ URL('media') }}/{{ $file }}" class="no-underline gray-500"
                               target="_blank">{{ URL('media') }}/<strong>{{ $file }}</strong></a></td>
                    @else
                        <td>{{ $file }}</td>
                        <td><a href="{{ URL('media') }}/{{ $file }}" class="no-underline text-black"
                               target="_blank">{{ URL('media') }}/<strong>{{ $file }}</strong></a></td>
                    @endif

                    <?php
                      $fileName = str_replace('/', '::', $file);
                    ?>
                    @can('Delete File')
                    <td>
                        <delete-file-modal-component :del_name="'{{ $file }}'" :doublecollon_name="'{{ $fileName }}'"></delete-file-modal-component>
                    </td>
                    @endcan
                </tr>
                @endforeach

            </table>

        </div>

    </div>
</div>

@endsection
