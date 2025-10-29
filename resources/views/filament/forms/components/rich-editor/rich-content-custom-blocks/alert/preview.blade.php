<div class="alert-preview alert-preview-{{ $type }}">
    <div class="alert-preview-content">
        <div class="alert-preview-header">
            <span class="alert-preview-type">{{ ucfirst($type) }}</span>
            <h4 class="alert-preview-heading">{{ $heading }}</h4>
        </div>
        @if($content)
            <div class="alert-preview-body">
                {{ Str::limit(strip_tags($content), 100) }}
            </div>
        @endif
    </div>
</div>
