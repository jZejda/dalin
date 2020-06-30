{{-- Weather icons --}}
@if( $icon == 'cloudy' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud" aria-labelledby="title">
        <title>{{ $summary }}</title>
        <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
    </svg>


@elseif( $icon == 'clear-day' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun">
        <title>{{ $summary }}</title>
        <circle cx="12" cy="12" r="5"></circle>
        <line x1="12" y1="1" x2="12" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="23"></line>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
        <line x1="1" y1="12" x2="3" y2="12"></line>
        <line x1="21" y1="12" x2="23" y2="12"></line>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
    </svg>

@elseif( $icon == 'partly-cloudy-day' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="jirka partly-cloudy-day">
        <title>{{ $summary }}</title>

        <path d="M 20,22.97 A 5,5 0 0 0 18,13.39 H 16.74 A 8,8 0 1 0 4,21.64"></path>
        <path d="m 12.730296,5.2084924 c 0,0 2.384536,-1.9824228 5.34,0.06 3.303367,2.2828472 1.89,5.5499986 1.89,5.5499986"></path>
        <path d="M 20.324416,3.3641301 20.839493,2.873727"></path>
        <path d="M 16.550221,1.9239102 16.712082,1.2313777"></path>

        <path d="M 12.1658,2.3932366 11.805119,1.7802851"></path>
        <path d="M 22.323664,6.5815254 22.970163,6.2851455"></path>
        <path d="m 22.378605,11.000636 0.681471,0.203472"></path>
    </svg>


@elseif( $icon == 'clear-night' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon">
        <title>{{ $summary }}</title>
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
    </svg>

@elseif( $icon == 'rain' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-rain" aria-labelledby="title">
        <line x1="16" y1="13" x2="16" y2="21"></line>
        <line x1="8" y1="13" x2="8" y2="21"></line>
        <line x1="12" y1="15" x2="12" y2="23"></line>
        <path d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"></path>
    </svg>

@elseif( $icon == 'snow' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-snow" aria-labelledby="title">
        <path d="M20 17.58A5 5 0 0 0 18 8h-1.26A8 8 0 1 0 4 16.25"></path>
        <line x1="8" y1="16" x2="8.01" y2="16"></line>
        <line x1="8" y1="20" x2="8.01" y2="20"></line>
        <line x1="12" y1="18" x2="12.01" y2="18"></line>
        <line x1="12" y1="22" x2="12.01" y2="22"></line>
        <line x1="16" y1="16" x2="16.01" y2="16"></line>
        <line x1="16" y1="20" x2="16.01" y2="20"></line>
    </svg>

@elseif( $icon == 'wind' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-wind" aria-labelledby="title">
        <path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"></path>
    </svg>

@elseif( $icon == 'sleet' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-drizzle" aria-labelledby="title">
        <line x1="8" y1="19" x2="8" y2="21"></line>
        <line x1="8" y1="13" x2="8" y2="15"></line>
        <line x1="16" y1="19" x2="16" y2="21"></line>
        <line x1="16" y1="13" x2="16" y2="15"></line>
        <line x1="12" y1="21" x2="12" y2="23"></line>
        <line x1="12" y1="15" x2="12" y2="17"></line>
        <path d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"></path>
    </svg>


@elseif( $icon == 'none' )
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-off" aria-labelledby="title">
        <title>{{ $summary }}</title>
        <path d="M22.61 16.95A5 5 0 0 0 18 10h-1.26a8 8 0 0 0-7.05-6M5 5a8 8 0 0 0 4 15h9a5 5 0 0 0 1.7-.3"></path><line x1="1" y1="1" x2="23" y2="23"></line>
    </svg>

@else()
    <svg xmlns="http://www.w3.org/2000/svg" width="{{ $ico_size }}" height="{{ $ico_size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-off" aria-labelledby="title">
        <title>{{ $summary }}</title>
        <path d="M22.61 16.95A5 5 0 0 0 18 10h-1.26a8 8 0 0 0-7.05-6M5 5a8 8 0 0 0 4 15h9a5 5 0 0 0 1.7-.3"></path><line x1="1" y1="1" x2="23" y2="23"></line>
    </svg>
@endif

{{-- fog, partly-cloudy-night --}}
