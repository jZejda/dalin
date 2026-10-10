@props([
    'label',
    'quoted' => false,
])
{{ $label }}:
@if ($quoted)
{{ __('mail/common.club_layout.quote_open') }}{{ $slot }}{{ __('mail/common.club_layout.quote_close') }}
@else
{{ $slot }}
@endif
