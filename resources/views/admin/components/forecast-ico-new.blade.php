@if( $icon == 'cloudy' )
    <p title="{{ $summary }}"><i class="pe-is-w-mostly-cloudy-2 pe-3x pe-va"></i></p>

@elseif( $icon == 'clear-day' )
    <p title="{{ $summary }}"><i class="pe-is-w-sun-1 pe-3x pe-va"></i></p>

@elseif( $icon == 'partly-cloudy-day' )
    <p title="{{ $summary }}"><i class="pe-is-w-partly-cloudy-1 pe-3x pe-va"></i></p>

@elseif( $icon == 'clear-night' )
    <p title="{{ $summary }}"><i class="pe-is-w-moon-1 pe-3x pe-va"></i></p>


@elseif( $icon == 'rain' )
    <p title="{{ $summary }}"><i class="pe-is-w-rain-1 pe-3x pe-va"></i></p>

@elseif( $icon == 'snow' )
    <p title="{{ $summary }}"><i class="pe-is-w-snow pe-3x pe-va"></i></p>

@elseif( $icon == 'wind' )
    <p title="{{ $summary }}"><i class="pe-is-w-wind pe-3x pe-va"></i></p>

@elseif( $icon == 'sleet' )
    <p title="{{ $summary }}"><i class="pe-is-w-thunderstorm-day-2 pe-3x pe-va"></i></p>

@elseif( $icon == 'fog' )
    <p title="{{ $summary }}"><i class="pe-is-w-fog-2 pe-3x pe-va"></i></p>


@elseif( $icon == 'none' )
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-off" aria-labelledby="title">
        <title>{{ $summary }}</title>
        <path d="M22.61 16.95A5 5 0 0 0 18 10h-1.26a8 8 0 0 0-7.05-6M5 5a8 8 0 0 0 4 15h9a5 5 0 0 0 1.7-.3"></path><line x1="1" y1="1" x2="23" y2="23"></line>
    </svg>

@else()
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-off" aria-labelledby="title">
        <title>{{ $summary }}</title>
        <path d="M22.61 16.95A5 5 0 0 0 18 10h-1.26a8 8 0 0 0-7.05-6M5 5a8 8 0 0 0 4 15h9a5 5 0 0 0 1.7-.3"></path><line x1="1" y1="1" x2="23" y2="23"></line>
    </svg>
@endif

{{-- fog, partly-cloudy-night --}}
