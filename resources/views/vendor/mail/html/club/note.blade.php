@props(['label'])
<table class="club-note club-soft" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<p class="club-note-label">{{ $label }}</p>
<p>{{ $slot }}</p>
</td>
</tr>
</table>
