@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'settingsUrl' => null,
])
@php
    $brand = \App\Services\Mail\MailBranding::fromSettings();
@endphp
{{ $brand->clubName }}

@if (filled($eyebrow))
{{ $eyebrow }}
@endif
@if (filled($title))
{{ $title }}
@endif
@if (filled($lead))
{{ $lead }}
@endif

{{ $slot }}

---
{{ __('mail/common.club_layout.footer_sender', ['club' => $brand->clubName, 'product' => \App\Services\Mail\MailBranding::PRODUCT_NAME]) }}
{{ __('mail/common.club_layout.help') }}: {{ $brand->helpUrl }}
@if ($brand->contactEmail !== null)
{{ __('mail/common.club_layout.contact') }}: {{ $brand->contactEmail }}
@endif
@if (filled($settingsUrl))
{{ __('mail/common.club_layout.notification_settings') }}: {{ $settingsUrl }}
@endif
@foreach ($brand->socialLinks as $social)
{{ $social['label'] }}: {{ $social['url'] }}
@endforeach
