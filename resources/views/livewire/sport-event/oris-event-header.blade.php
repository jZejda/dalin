@php
    use App\Enums\OrisPaymentState;
    use App\Services\Mail\MailMoney;
    use App\Services\OrisApiService;
    use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
    use Illuminate\Support\Js;

    /** @var \App\Models\SportEvent $sportEvent */
    /** @var \App\Models\SportEventOrisStatus|null $status */
    /** @var OrisPaymentState|null $paymentState */
    /** @var \App\Services\Payment\QrPayment|null $qrPayment */

    $orisUrl = fn (string $page): string => OrisApiService::ORIS_URL.'/'.$page.'?id='.$sportEvent->oris_id;
    $money = fn (?float $amount): string => MailMoney::format((float) $amount, $status->currency ?? 'CZK');
    // Without a loaded status we do not know — keep the links neutral instead of claiming "not published yet"
    $hasStartList = $status === null || $status->hasStartList();
    $hasResults = $status === null || $status->hasResults();
@endphp

{{-- Row 1: ORIS logo, then (set apart) start list and results; row 2: payment pill under the logo --}}
<div class="flex flex-col items-start gap-2" wire:init="refreshIfStale">
    <div class="flex items-center gap-6">
        <a
            href="{{ $orisUrl('Zavod') }}"
            target="_blank"
            rel="noopener"
            class="inline-flex shrink-0 rounded-md focus-visible:outline-2 focus-visible:outline-primary-500"
            aria-label="{{ __('sport-event.oris_status.oris_link') }}"
            x-tooltip="{ content: {{ Js::from(__('sport-event.oris_status.oris_link')) }}, theme: $store.theme }"
        >
            <img src="{{ asset('images/brands/oris-logo.svg') }}" alt="ORIS" class="h-7 w-auto" />
        </a>

        <div class="flex items-center gap-1">
            <x-filament::icon-button
                tag="a"
                :href="$orisUrl('Startovka')"
                target="_blank"
                :icon="LucideIcon::ListOrdered"
                :color="$hasStartList ? 'primary' : 'gray'"
                :label="__('sport-event.oris_status.start_list')"
                :tooltip="$hasStartList ? __('sport-event.oris_status.start_list') : __('sport-event.oris_status.start_list_missing')"
                :class="$hasStartList ? null : 'opacity-50'"
            />
            <x-filament::icon-button
                tag="a"
                :href="$orisUrl('Vysledky')"
                target="_blank"
                :icon="LucideIcon::Trophy"
                :color="$hasResults ? 'primary' : 'gray'"
                :label="__('sport-event.oris_status.results')"
                :tooltip="$hasResults ? __('sport-event.oris_status.results') : __('sport-event.oris_status.results_missing')"
                :class="$hasResults ? null : 'opacity-50'"
            />
        </div>
    </div>

    @if ($paymentState !== null && $status !== null)
        <x-filament::dropdown placement="bottom-start" width="xs" teleport>
            <x-slot name="trigger">
                <button type="button" class="inline-flex rounded-md focus-visible:outline-2 focus-visible:outline-primary-500">
                    <x-filament::badge :color="$paymentState->getColor()" :icon="$paymentState->getIcon()" size="lg">
                        {{ __('sport-event.oris_status.pill.'.$paymentState->value, ['amount' => $money(abs((float) $status->to_be_paid))]) }}
                    </x-filament::badge>
                </button>
            </x-slot>

            <div class="space-y-3 p-4 text-sm text-gray-950 dark:text-white">
                <p class="font-semibold">{{ __('sport-event.oris_status.popover.heading') }}</p>

                <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('sport-event.oris_status.popover.fee_total') }}</dt>
                    <dd class="text-right font-medium tabular-nums">{{ $money($status->fee_total) }}</dd>
                    <dd class="col-span-2 text-right text-xs text-gray-500 dark:text-gray-400">
                        {{ __('sport-event.oris_status.popover.fee_breakdown', [
                            'entry' => $money($status->fee_entry),
                            'si' => $money($status->fee_si),
                            'service' => $money($status->fee_service),
                        ]) }}
                    </dd>

                    <dt class="text-gray-500 dark:text-gray-400">{{ __('sport-event.oris_status.popover.paid') }}</dt>
                    <dd class="text-right font-medium tabular-nums">{{ $money($status->paid) }}</dd>

                    <dt class="text-gray-500 dark:text-gray-400">{{ __('sport-event.oris_status.popover.to_be_paid') }}</dt>
                    <dd @class([
                        'text-right font-semibold tabular-nums',
                        'text-danger-600 dark:text-danger-400' => $paymentState->isOutstanding(),
                        'text-success-600 dark:text-success-400' => ! $paymentState->isOutstanding(),
                    ])>{{ $money(max((float) $status->to_be_paid, 0)) }}</dd>
                </dl>

                @if ($status->bank_account !== null || $status->payment_vs !== null)
                    <dl class="grid grid-cols-[auto_1fr_auto] items-center gap-x-2 gap-y-1 border-t border-gray-200 pt-3 dark:border-white/10">
                        @foreach (['bank_account' => $status->bank_account, 'payment_vs' => $status->payment_vs] as $field => $value)
                            @if ($value !== null)
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('sport-event.oris_status.popover.'.$field) }}</dt>
                                <dd class="text-right font-mono">{{ $value }}</dd>
                                <dd>
                                    <x-filament::icon-button
                                        :icon="LucideIcon::Copy"
                                        color="gray"
                                        size="sm"
                                        :label="__('sport-event.oris_status.popover.copy')"
                                        x-on:click="
                                            window.navigator.clipboard.writeText({{ Js::from($value) }})
                                            $tooltip({{ Js::from(__('sport-event.oris_status.popover.copied')) }}, { theme: $store.theme, timeout: 2000 })
                                        "
                                    />
                                </dd>
                            @endif
                        @endforeach
                    </dl>
                @endif

                @if ($qrPayment !== null)
                    <figure class="flex flex-col items-center gap-1 border-t border-gray-200 pt-3 dark:border-white/10">
                        <img src="{{ $qrPayment->toSvgDataUri() }}" alt="{{ __('sport-event.oris_status.popover.qr_alt') }}" class="h-36 w-36 rounded bg-white" />
                        <figcaption class="text-xs text-gray-500 dark:text-gray-400">{{ __('sport-event.oris_status.popover.qr_hint') }}</figcaption>
                    </figure>
                @endif

                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('sport-event.oris_status.popover.source_note') }}</p>

                <div class="flex items-center justify-between gap-2 border-t border-gray-200 pt-2 text-xs text-gray-500 dark:border-white/10 dark:text-gray-400">
                    <span>{{ __('sport-event.oris_status.popover.fetched_at', ['time' => $status->fetched_at->format('j. n. Y H:i')]) }}</span>
                    @if ($canRefresh)
                        <x-filament::icon-button
                            :icon="LucideIcon::RefreshCw"
                            color="gray"
                            size="sm"
                            wire:click="refreshFromOris"
                            :label="__('sport-event.oris_status.popover.refresh')"
                            :tooltip="__('sport-event.oris_status.popover.refresh')"
                        />
                    @endif
                </div>
            </div>
        </x-filament::dropdown>
    @endif
</div>
