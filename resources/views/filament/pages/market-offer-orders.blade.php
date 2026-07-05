<div class="space-y-6">
    @foreach ($offer->products as $product)
        <div>
            <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                {{ $product->name }}
                @if (! $product->hasUnlimitedQty())
                    <span class="font-normal text-gray-500 dark:text-gray-400">
                        ({{ __('marketplace.qty_remaining') }}: {{ $product->qtyRemaining() }} / {{ $product->qty_available }})
                    </span>
                @endif
            </h3>

            @if ($product->orders->isEmpty())
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('marketplace.no_orders_yet') }}
                </p>
            @else
                <ul class="mt-2 divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($product->orders->sortByDesc('created_at') as $order)
                        <li class="flex items-center justify-between gap-4 py-2 text-sm">
                            <div>
                                <span class="font-medium text-gray-950 dark:text-white">
                                    {{ $order->user?->name }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    · {{ $order->qty }} ks
                                    @unless ($order->unit_price === 0.0)
                                        · {{ number_format($order->totalAmount(), 2, ',', ' ') }} {{ __('marketplace.price_suffix') }}
                                    @endunless
                                </span>
                                @if ($order->note)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->note }}</p>
                                @endif
                            </div>
                            <x-filament::badge :color="$order->status->getColor()">
                                {{ $order->status->getLabel() }}
                            </x-filament::badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
</div>
