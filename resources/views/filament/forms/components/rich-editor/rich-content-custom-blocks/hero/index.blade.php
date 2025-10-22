<div class="hero-block">
    <div class="hero-content">
        <h1 class="hero-heading">{{ $heading }}</h1>
        @if($subheading)
            <p class="hero-subheading">{{ $subheading }}</p>
        @endif
        @if(isset($buttonLabel) && isset($buttonUrl))
            <a href="{{ $buttonUrl }}" class="hero-button">
                {{ $buttonLabel }}
            </a>
        @endif
    </div>
</div>
