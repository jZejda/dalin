@props([
    'label',
    'amount',
    'note' => null,
])
<table class="club-balance club-soft" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<p class="club-balance-label">{{ $label }}</p>
<p class="club-balance-amount">{{ $amount }}</p>
@if (filled($note))
<p class="club-balance-note">{{ $note }}</p>
@endif
</td>
</tr>
</table>
