{{-- \resources\views\admin\roles\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Oevents')

@section('content')

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Akce</h1>
            </div>
        </div>
    </div>


    <!-- main content -->
    <div class="flex">
        <div class="w-3/4">

            <form class="w-full p-6" method="POST" action="{{ route('oevents.store') }}">
            @csrf

            <!-- Name/Place -->
                <div class="flex">
                    <div class="w-1/3 px-1">

                        <label for="title" class="form-label">
                            {{ __('Jméno akce') }}
                            @if ($errors->has('title'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('title') }}</span>
                            @endif
                        </label>
                        <input id="title" type="text" class="form-input-full {{ $errors->has('title') ? ' border-red-500 bg-white ' : '' }}" name="title" value="{{ old('title') }}">
                    </div>

                    <div class="w-1/3 px-1">
                        <label for="place" class="form-label">
                            {{ __('Místo konání') }}
                            @if ($errors->has('place'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('place') }}</span>
                            @endif
                        </label>
                        <input id="place" type="text" class="form-input-full {{ $errors->has('place') ? ' border-red-500 bg-white ' : '' }}" name="place" value="{{ old('place') }}">
                    </div>

                    <div class="w-1/3 px-1">
                        <label for="oris_id" class="form-label">
                            {{ __('Oris ID') }}
                            @if ($errors->has('oris_id'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('oris_id') }}</span>
                            @endif
                        </label>
                        <input id="oris_id" type="text" class="form-input-full {{ $errors->has('oris_id') ? ' border-red-500 bg-white ' : '' }}" name="oris_id" value="{{ old('oris_id') }}">
                    </div>
                </div>

                <!-- Coorfinates lat/lon -->
                <!--
                <div class="flex">
                    <div class="w-1/2 px-1">

                        <label for="lat" class="form-label">
                            {{ __('LAT') }}
                            @if ($errors->has('lat'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('lat') }}</span>
                            @endif
                        </label>
                        <input id="lat" type="text" class="form-input-full {{ $errors->has('lat') ? ' border-red-500 bg-white ' : '' }}" name="lat" value="{{ old('lat') }}">


                    </div>

                    <div class="w-1/2 px-1">

                        <label for="lon" class="form-label">
                            {{ __('LON') }}
                            @if ($errors->has('lon'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('lon') }}</span>
                            @endif
                        </label>
                        <input id="lon" type="text" class="form-input-full {{ $errors->has('lon') ? ' border-red-500 bg-white ' : '' }}" name="lon" value="{{ old('lon') }}">


                    </div>
                </div>
                -->


                <!-- From/To -->
                <!--
                <div class="flex">
                    <div class="w-1/2 px-1">

                        <label for="date_from" class="form-label">
                            {{ __('Datum od') }}
                            @if ($errors->has('date_from'))
                                <span class="form-label-error">
                                @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('date_from') }}
                            </span>
                            @endif
                        </label>
                        <date-input-component
                            :date_input_value="'{{ null !== old('date_from') ? \Carbon\Carbon::createFromFormat('d.m.Y', old('date_from'))->toISOString() : '' }}'"
                            :date_input_name="'date_from'"
                        >
                        </date-input-component>


                    </div>

                    <div class="w-1/2 px-1">

                        <label for="date_to" class="form-label">
                            {{ __('Datum do') }}
                            @if ($errors->has('date_to'))
                                <span class="form-label-error">
                                @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('date_to') }}
                            </span>
                            @endif
                        </label>
                        <date-input-component
                            :date_input_value="'{{ null !== old('date_to') ? \Carbon\Carbon::createFromFormat('d.m.Y', old('date_to'))->toISOString() : '' }}'"
                            :date_input_name="'date_to'"
                        >
                        </date-input-component>
                    </div>
                </div>
                -->

                <!-- Sport/url -->
                <div class="flex">
                    <div class="w-1/3 px-1">

                        <label for="sport_id" class="form-label">
                            {{ __('Sport') }}
                            @if ($errors->has('sport_id'))
                                <span class="form-label-error">
                            @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('sport_id') }}
                        </span>
                            @endif
                        </label>
                        {{ Form::select('sport_id', $sport, null, array(
                            'name' => 'sport_id',
                            'class' => $errors->has('sport_id') ? ' form-input-full bg-white border-red-500' : ' form-input-full'
                        ))}}

                    </div>

                    <div class="w-2/3 px-1">

                        <label for="url" class="form-label">
                            {{ __('Odkaz na stránky') }}
                            @if ($errors->has('url'))
                                <span class="form-label-error">
                            @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('url') }}
                    </span>
                            @endif
                        </label>
                        <input id="url" type="text"
                               class="form-input-full {{ $errors->has('url') ? ' form-input-error ' : '' }}"
                               name="second_date" value="{{ old('url') }}">
                    </div>


                </div>
                <!-- First/Secont/Third -->
                <div class="flex">
                    <div class="w-1/2 px-1">

                        <label for="from_date" class="form-label">
                            {{ __('Akce od') }}
                            @if ($errors->has('from_date'))
                                <span class="form-label-error">
                                @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('from_date') }}
                        </span>
                            @endif
                        </label>
                        <date-input-component
                            :date_input_value="'{{ null !== old('from_date') ? \Carbon\Carbon::createFromFormat('d.m.Y', old('from_date'))->subHour()->toISOString() : '' }}'"
                            :date_input_name="'from_date'"
                        >
                        </date-input-component>
                        {{--
                        <input id="from_date" placeholder="DD.MM.RRRR" type="text"
                               class="form-input-full {{ $errors->has('from_date') ? ' form-input-error ' : '' }}"
                               name="from_date" value="{{ old('from_date') }}">
                               --}}
                    </div>

                    <div class="w-1/2 px-1">

                        <label for="to_date" class="form-label">
                            {{ __('Akce do') }}
                            @if ($errors->has('to_date'))
                                <span class="form-label-error">
                                @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('to_date') }}
                        </span>
                            @endif
                        </label>
                        <date-input-component
                            :date_input_value="'{{ null !== old('to_date') ? \Carbon\Carbon::createFromFormat('d.m.Y', old('to_date'))->subHour()->toISOString() : '' }}'"
                            :date_input_name="'to_date'"
                        >
                        </date-input-component>
                        {{--
                        <input id="to_date" type="text"
                               class="form-input-full {{ $errors->has('to_date') ? ' form-input-error ' : '' }}"
                               name="to_date" value="{{ old('to_date') }}">
                               --}}
                    </div>
                </div>

                <!-- First/Secont/Third -->
                <div class="flex">
                    <div class="w-1/3 px-1">

                        <label for="first_date" class="form-label">
                            {{ __('První termín') }}
                            @if ($errors->has('first_date'))
                                <span class="form-label-error">
                                    @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('first_date') }}
                                </span>
                            @endif
                        </label>
                        <date-time-input-component
                            :date_input_value="'{{ null !== old('first_date') ? \Carbon\Carbon::createFromFormat('d.m.Y H:i:s', old('first_date'))->subHour()->toISOString() : '' }}'"
                            :date_input_name="'first_date'"
                        >
                        </date-time-input-component>
                        {{--
                        <input id="first_date" placeholder="DD.MM.RRRR HH:MM:SS" type="text"
                               class="form-input-full {{ $errors->has('first_date') ? ' form-input-error ' : '' }}"
                               name="first_date" value="{{ old('first_date') }}">
                               --}}
                    </div>

                    <div class="w-1/3 px-1">

                        <label for="second_date" class="form-label">
                            {{ __('Druhý termín') }}
                            @if ($errors->has('second_date'))
                                <span class="form-label-error">
                                @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('second_date') }}
                        </span>
                            @endif
                        </label>
                        <date-time-input-component
                            :date_input_value="'{{ null !== old('second_date') ? \Carbon\Carbon::createFromFormat('d.m.Y H:i:s', old('second_date'))->subHour()->toISOString() : '' }}'"
                            :date_input_name="'second_date'"
                        >
                        </date-time-input-component>
                        {{--
                        <input id="second_date" type="text"
                               class="form-input-full {{ $errors->has('second_date') ? ' form-input-error ' : '' }}"
                               name="second_date" value="{{ old('second_date') }}">
                               --}}
                    </div>
                    <div class="w-1/3 px-1">

                        <label for="third_date" class="form-label">
                            {{ __('Třetí termín') }}
                            @if ($errors->has('third_date'))
                                <span class="form-label-error">
                                    @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('third_date') }}
                                </span>
                            @endif
                        </label>

                        <date-time-input-component
                            :date_input_value="'{{ null !== old('third_date') ? \Carbon\Carbon::createFromFormat('d.m.Y H:i:s', old('third_date'))->subHour()->toISOString() : '' }}'"
                            :date_input_name="'third_date'"
                        >
                        </date-time-input-component>
                        {{--
                        <input id="third_date" type="text"
                               class="form-input-full {{ $errors->has('third_date') ? ' form-input-error ' : '' }}"
                               name="third_date" value="{{ old('third_date') }}">
                               --}}
                    </div>
                </div>


                <!-- First/Secont/Third -->
                <div class="flex">
                    <div class="w-1/3 px-1">

                        <label for="clubs" class="form-label">
                            {{ __('Kluby') }}
                            @if ($errors->has('clubs'))
                                <span class="form-label-error">
                            @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('clubs') }}
                    </span>
                            @endif
                        </label>
                        {{Form::select('clubs',$clubs,null,array('
                            multiple'=>'multiple',
                            'name'=>'clubs[]',
                            'size'  => 4,
                            'class' => $errors->has('clubs') ? ' form-input-full bg-white border-red-500' : ' form-input-full'
                        ))}}

                    </div>

                    <div class="w-1/3 px-1">
                        <label for="regions" class="form-label">
                            {{ __('Regiony') }}
                            @if ($errors->has('regions'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('regions') }}</span>
                            @endif
                        </label>
                        {{Form::select('regions',$regions,null,array('
                            multiple'=>'multiple',
                            'name'=>'regions[]',
                            'size'  => 4,
                            'class' => $errors->has('regions') ? ' form-input-full bg-white border-red-500' : ' form-input-full'
                        ))}}

                    </div>
                    <div class="w-1/3 px-1">

                        <label for="event_category" class="form-label">
                            {{ __('Typ akce') }}
                            @if ($errors->has('event_category'))
                                <span class="form-label-error">
                            @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('event_category') }}
                    </span>
                            @endif
                        </label>
                        {{ Form::select('event_category', [1 => 'Závod', 2 => 'Trénink', 3 => 'Soustředění', 4 => 'Tábor'], null, array(
                            'name' => 'event_category',
                            'class' => $errors->has('event_category') ? ' form-input-full bg-white border-red-500' : ' form-input-full'
                        ))}}

                    </div>
                </div>
                <div class="flex">
                    <div class="w-full">

                        <label for="description" class="form-label">
                            {{ __('Popis akce') }}
                            @if ($errors->has('description'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('description') }}</span>
                            @endif
                        </label>
                        <textarea rows="4" cols="50" id="description" type="text" class="form-input-full {{ $errors->has('description') ? ' border-red-500 bg-white ' : '' }}" name="description" value="{{ old('description') }}">{{ old('description') }}</textarea>

                    </div>
                </div>

                <div class="w-full px-1">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('Uložit akci') }}
                    </button>
                </div>


            </form>

        </div>
        <!-- right content -->
        <div id="content-left-sidebar" class="w-1/2 border-l h-screen bg-gray-300">
            <div class="px-6 py-4 ">

                <!-- Item iterate -->
                <p>levypanel</p>
            </div>
        </div>
    </div>


@endsection

@section('pageCustomJS')

@endsection
