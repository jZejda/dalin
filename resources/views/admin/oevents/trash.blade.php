<!-- Lat/Lon -->
<div class="flex">
    <div class="w-1/2 px-1">

        <label for="lat" class="form-label">
            {{ __('Lat') }}
            @if ($errors->has('lat'))
                <span class="form-label-error">
                                    @component('admin.components.form-label-error-ico')@endcomponent
                    {{ $errors->first('lat') }}
                                </span>
            @endif
        </label>
        <input type="text" name="lat">fsfsfs</input>
        <multiselect-input-component :name="lat"></multiselect-input-component>
    <!--<input id="lat" type="text" class="form-input-full {{ $errors->has('lat') ? ' border-red-500 bg-white ' : '' }}" name="lat" value="{{ old('lat') }}"> -->

    </div>

    <div class="w-1/2 px-1">

        <label for="lon" class="form-label">
            {{ __('Lon') }}
            @if ($errors->has('lon'))
                <span class="form-label-error">
                                    @component('admin.components.form-label-error-ico')@endcomponent
                    {{ $errors->first('lon') }}
                                </span>
            @endif
        </label>
        <input id="lon" type="text" class="form-input-full {{ $errors->has('lon') ? ' border-red-500 bg-white ' : '' }}" name="lon" value="{{ old('lon') }}">

    </div>
</div>

<!-- Sport/Clubs/Rank -->
<div class="flex">
    <div class="w-1/3 px-1">

        <label for="sport" class="form-label">
            {{ __('Sport') }}
            @if ($errors->has('sport'))
                <span class="form-label-error">
                                    @component('admin.components.form-label-error-ico')@endcomponent
                    {{ $errors->first('sport') }}
                                </span>
            @endif
        </label>
        <input id="sport" type="text" class="form-input-full {{ $errors->has('sport') ? ' border-red-500 bg-white ' : '' }}" name="sport" value="{{ old('sport') }}">

    </div>

    <div class="w-1/3 px-1">

        <label for="clubs" class="form-label">
            {{ __('Klub/y') }}
            @if ($errors->has('clubs'))
                <span class="form-label-error">
                                @component('admin.components.form-label-error-ico')@endcomponent
                    {{ $errors->first('clubs') }}
                            </span>
            @endif
        </label>
        <input id="clubs" type="text" class="form-input-full {{ $errors->has('clubs') ? ' border-red-500 bg-white ' : '' }}" name="clubs" value="{{ old('clubs') }}">

    </div>

    <div class="w-1/3 px-1">

        <label for="rank" class="form-label">
            {{ __('Ranking') }}
            @if ($errors->has('rank'))
                <span class="form-label-error">
                                @component('admin.components.form-label-error-ico')@endcomponent
                    {{ $errors->first('rank') }}
                            </span>
            @endif
        </label>
        <input id="rank" type="text" class="form-input-full {{ $errors->has('rank') ? ' border-red-500 bg-white ' : '' }}" name="rank" value="{{ old('rank') }}">

    </div>
</div>



$table->integer('orisid')->nullable();
$table->string('place', 255);
$table->json('clubs');
$table->string('url', 255);
$table->tinyInteger('rank')->default(1)->unsigned();
$table->tinyInteger('legs')->default(1);
$table->longText('description');
$table->tinyInteger('content_mode')->unsigned()->default(2);
