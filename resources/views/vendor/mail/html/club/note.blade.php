@props([
    'label',
    'quoted' => false,
])
<table class="club-note club-soft" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<p class="club-note-label">{{ $label }}</p>
<p>@if ($quoted){{ __('mail/common.club_layout.quote_open') }}{{ $slot }}{{ __('mail/common.club_layout.quote_close') }}@else{{ $slot }}@endif</p>
</td>
</tr>
</table>
