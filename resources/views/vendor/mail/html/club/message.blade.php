@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'settingsUrl' => null,
])
@php
    $brand = \App\Services\Mail\MailBranding::fromSettings();
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<style>
@media only screen and (max-width: 640px) {
.club-outer { padding: 16px 0 !important; }
.club-card { width: 100% !important; border-radius: 0 !important; }
.club-stripe { border-radius: 0 !important; }
.club-pad { padding-left: 18px !important; padding-right: 18px !important; }
.club-wordmark { display: none !important; }
.club-title { font-size: 23px !important; }
.club-fact { display: block !important; width: 100% !important; }
.club-button { display: block !important; text-align: center !important; }
.club-secondary { display: block !important; padding: 14px 0 0 !important; text-align: center !important; }
}
@media (prefers-color-scheme: dark) {
.club-wrapper, body { background-color: #151c20 !important; }
.club-card { background-color: #222b30 !important; border-color: #43514b !important; }
.club-name, .club-eyebrow, .club-title, .club-body, .club-body p, .club-body a, .club-date, .club-event-name a, .club-fact-value, .club-person-name, .club-secondary a { color: #eef3f1 !important; }
.club-tagline, .club-wordmark, .club-lead, .club-section, .club-meta, .club-person-extra, .club-note-label, .club-fine, .club-footer, .club-footer a { color: #b5c3bf !important; }
.club-soft { background-color: #2c3834 !important; }
.club-pill { background-color: #2c3834 !important; color: #eef3f1 !important; }
.club-event, .club-person, .club-footer { border-color: #43514b !important; }
.club-secondary-negative a { color: #ffb0b4 !important; }
}
</style>
</head>
<body class="club-wrapper">
<table class="club-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="club-outer" align="center">
<table class="club-card" align="center" width="620" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="club-stripe">&nbsp;</td>
</tr>
<tr>
<td class="club-pad club-header">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td width="42" valign="middle">
@if ($brand->logoUrl !== null)
<img class="club-logo" src="{{ $brand->logoUrl }}" width="42" height="42" alt="{{ $brand->clubName }}">
@else
<table cellpadding="0" cellspacing="0" role="presentation"><tr><td class="club-initials" width="42" height="42" align="center" valign="middle">{{ $brand->clubInitials }}</td></tr></table>
@endif
</td>
<td class="club-brand" valign="middle">
<span class="club-name">{{ $brand->clubName }}</span>
<span class="club-tagline">{{ __('mail/common.club_layout.tagline') }}</span>
</td>
<td class="club-wordmark" align="right" valign="middle">{{ \App\Services\Mail\MailBranding::PRODUCT_NAME }}</td>
</tr>
</table>
</td>
</tr>
@if (filled($eyebrow) || filled($title) || filled($lead))
<tr>
<td class="club-pad club-hero">
@if (filled($eyebrow))
<p class="club-eyebrow">{{ $eyebrow }}</p>
@endif
@if (filled($title))
<h1 class="club-title">{{ $title }}</h1>
@endif
@if (filled($lead))
<p class="club-lead">{{ $lead }}</p>
@endif
</td>
</tr>
@endif
<tr>
<td class="club-pad club-body">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
<tr>
<td class="club-pad club-footer">
<p>{{ __('mail/common.club_layout.footer_sender', ['club' => $brand->clubName, 'product' => \App\Services\Mail\MailBranding::PRODUCT_NAME]) }}</p>
<p>
<a href="{{ $brand->helpUrl }}">{{ __('mail/common.club_layout.help') }}</a>
@if ($brand->contactEmail !== null)
&middot; <a href="mailto:{{ $brand->contactEmail }}">{{ __('mail/common.club_layout.contact') }}</a>
@endif
@if (filled($settingsUrl))
&middot; <a href="{{ $settingsUrl }}">{{ __('mail/common.club_layout.notification_settings') }}</a>
@endif
</p>
@if ($brand->socialLinks !== [])
<p>
@foreach ($brand->socialLinks as $social)
<a href="{{ $social['url'] }}">{{ $social['label'] }}</a>@if (! $loop->last) &middot; @endif
@endforeach
</p>
@endif
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
