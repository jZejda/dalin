@props(['sum' => 0.0, 'count' => 0])

{{-- Rendered by Filament inside <tfoot><tr>, so the content has to be a table cell spanning all columns. --}}
<td colspan="100" class="border-t border-gray-200 px-4 py-3 dark:border-white/10">
    <div class="flex items-center justify-end gap-6">
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('sport-event.payment_list.total_event') }}</span>
        <span class="text-lg font-semibold"><x-money-amount :amount="$sum" /></span>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $count }} {{ trans_choice('sport-event.payment_list.payments_word', $count) }}
        </span>
    </div>
</td>
