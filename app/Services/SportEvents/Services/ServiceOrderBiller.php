<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Services;

use App\Enums\ServiceOrderStatus;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\SportEvent;
use App\Models\SportServiceOrder;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use Illuminate\Support\Facades\DB;

class ServiceOrderBiller
{
    /**
     * Charge every ordered service of the event to the user credits and mark
     * the orders as billed. Already billed or cancelled orders are skipped,
     * so the operation can be safely repeated.
     *
     * @return int number of billed orders
     */
    public function billEvent(SportEvent $sportEvent, User $billedBy): int
    {
        $orders = SportServiceOrder::query()
            ->where('sport_event_id', '=', $sportEvent->id)
            ->where('status', '=', ServiceOrderStatus::Ordered->value)
            ->with(['sportService', 'paymentDate'])
            ->get();

        $billed = 0;

        DB::transaction(function () use ($orders, $sportEvent, $billedBy, &$billed): void {
            foreach ($orders as $order) {
                $credit = new UserCredit();
                $credit->user_id = $order->user_id;
                $credit->user_race_profile_id = $order->user_race_profile_id;
                $credit->sport_event_id = $sportEvent->id;
                $credit->sport_service_id = $order->sport_service_id;
                $credit->sport_service_order_id = $order->id;
                $credit->amount = -$order->totalAmount();
                $credit->currency = UserCredit::CURRENCY_CZK;
                $credit->credit_type = UserCreditType::ServiceFee;
                $credit->source = UserCreditSource::User->value;
                $credit->source_user_id = $billedBy->id;
                $credit->status = UserCreditStatus::Done;
                $credit->saveOrFail();

                $note = new UserCreditNote();
                $note->note_user_id = $billedBy->id;
                $note->user_credit_id = $credit->id;
                $note->note = 'Vyúčtování doplňkové služby: '.($order->sportService->service_name_cz ?? '#'.$order->sport_service_id)
                    .' ('.$order->qty.' ks × '.number_format($order->unit_price, 2, ',', ' ').' Kč).';
                $note->internal = true;
                $note->params = [
                    'sport_service_order_id' => $order->id,
                    'qty' => $order->qty,
                    'unit_price' => $order->unit_price,
                    'payment_date' => $order->paymentDate?->payment_date?->toDateString(),
                ];
                $note->saveOrFail();

                $order->status = ServiceOrderStatus::Billed;
                $order->saveOrFail();

                $billed++;
            }
        });

        return $billed;
    }
}
