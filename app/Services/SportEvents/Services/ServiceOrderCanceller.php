<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Services;

use App\Enums\ServiceOrderStatus;
use App\Models\SportServiceOrder;
use Illuminate\Support\Facades\DB;

class ServiceOrderCanceller
{
    public function __construct(
        private readonly OrisServiceEntryClient $orisClient = new OrisServiceEntryClient(),
    ) {
    }

    public function cancel(SportServiceOrder $order): ServiceOrderResult
    {
        if ($order->status !== ServiceOrderStatus::Ordered) {
            return ServiceOrderResult::failure('Zrušit lze pouze objednávku ve stavu Objednáno.');
        }

        if ($order->oris_service_entry_id !== null) {
            $orisResponse = $this->orisClient->deleteServiceEntry($order->oris_service_entry_id);

            if (! $orisResponse->isOk()) {
                return ServiceOrderResult::failure('ORIS zrušení objednávky odmítl: '.$orisResponse->status);
            }
        }

        DB::transaction(function () use ($order): void {
            $order->status = ServiceOrderStatus::Cancelled;
            $order->saveOrFail();

            $service = $order->sportService;
            if ($service === null) {
                return;
            }

            if ($service->qty_remaining !== null) {
                $service->qty_remaining += $order->qty;
            }
            if ($service->qty_already_ordered !== null) {
                $service->qty_already_ordered = max(0, $service->qty_already_ordered - $order->qty);
            }
            $service->save();
        });

        return new ServiceOrderResult(success: true, order: $order);
    }
}
