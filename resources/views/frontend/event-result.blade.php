{{-- \resources\views\frontend\event-result.blade.php --}}

@extends('layouts.frontend')

@section('title') Výsledky @endsection

@section('pageCustomCSS')
<link rel="stylesheet" href="{{ asset ("vendor/pe-icon-set-weather/css/pe-icon-set-weather.css") }}">
<link rel="stylesheet" href="{{ asset ("vendor/pe-icon-set-weather/css/helper.css") }}">
@endsection

<style>

    @import url('https://fonts.googleapis.com/css?family=Oswald&display=swap');

    .oswald_text {
        font-family: 'Oswald', sans-serif;
        font-weight:    500;
    }

</style>

@section('content')



    <div class="border-t-2 bg-gray-200 text-gray-800">
        <div class="container mx-auto app-front-content bg-white px-4">

            @if(empty($show_result))

                <p>Zádné výsledky tohoto typu tady nejsou.</p>

            @elseif(!empty($show_result) && $show_result['result_type'] == 'xml_iof_v3_file')

                <div class="oswald_text -mx-8 lg:mx-0">
                    <event-iofv3-result-component
                        :result_id = "{{ $show_result['result_id'] }}"
                        :oevent_url = "'{{ $oevent_url }}'"
                    >
                    </event-iofv3-result-component>
                </div>

            @endif

        </div>
    </div>

@endsection
