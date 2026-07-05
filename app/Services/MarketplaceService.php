<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MarketOfferStatus;
use App\Enums\MarketOrderStatus;
use App\Enums\MarketPaymentMethod;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Mail\MarketOfferAnnouncement;
use App\Mail\MarketOfferClosed;
use App\Models\MarketOffer;
use App\Models\MarketOrder;
use App\Models\MarketProduct;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class MarketplaceService
{
    /**
     * Vytvoří objednávku produktu. Zbývající množství se kontroluje
     * v transakci se zámkem produktu, aby dva uživatelé nemohli
     * objednat tentýž poslední kus. Cena se ukládá jako snapshot.
     */
    public function order(MarketProduct $product, User $user, int $qty, ?string $note = null): MarketOrder
    {
        if ($qty < 1) {
            throw new RuntimeException(__('marketplace.order_error_qty'));
        }

        return DB::transaction(function () use ($product, $user, $qty, $note): MarketOrder {
            /** @var MarketProduct $lockedProduct */
            $lockedProduct = MarketProduct::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $offer = $lockedProduct->marketOffer;

            if ($offer === null || ! $offer->isOpenForOrders()) {
                throw new RuntimeException(__('marketplace.order_error_closed'));
            }

            $remaining = $lockedProduct->qtyRemaining();

            if ($remaining !== null && $remaining < $qty) {
                throw new RuntimeException(__('marketplace.order_error_sold_out', ['remaining' => $remaining]));
            }

            return MarketOrder::query()->create([
                'market_product_id' => $lockedProduct->id,
                'user_id' => $user->id,
                'qty' => $qty,
                'unit_price' => $lockedProduct->unit_price,
                'note' => $note,
                'status' => MarketOrderStatus::Ordered,
            ]);
        });
    }

    /**
     * Zruší objednávku a uvolní alokované kusy. Zrušit lze jen
     * nevyúčtovanou objednávku, dokud je nabídka otevřená.
     */
    public function cancelOrder(MarketOrder $order): void
    {
        if ($order->status !== MarketOrderStatus::Ordered) {
            throw new RuntimeException(__('marketplace.cancel_error_status'));
        }

        $offer = $order->marketProduct?->marketOffer;

        if ($offer === null || ! $offer->isOpenForOrders()) {
            throw new RuntimeException(__('marketplace.cancel_error_closed'));
        }

        $order->update(['status' => MarketOrderStatus::Cancelled]);
    }

    /**
     * Předčasně uzavře nabídku a rozešle jednorázový e-mail všem
     * zúčastněným (zadavatel + objednatelé). Členové už nebudou moci
     * objednávat ani rušit objednávky.
     */
    public function closeOffer(MarketOffer $offer): void
    {
        if ($offer->status !== MarketOfferStatus::Active) {
            throw new RuntimeException(__('marketplace.close_error_status'));
        }

        $offer->update([
            'status' => MarketOfferStatus::Closed,
            'closed_at' => now(),
        ]);

        $this->notifyOfferClosed($offer);
    }

    /**
     * Rozúčtuje uzavřenou nabídku: objednávkám se stržením z konta
     * vytvoří kreditní záznam, všechny objednané položky označí jako
     * rozúčtované (přímá platba probíhá mimo systém).
     *
     * @return int počet rozúčtovaných objednávek
     */
    public function billOffer(MarketOffer $offer, User $billedBy): int
    {
        if ($offer->status !== MarketOfferStatus::Closed) {
            throw new RuntimeException(__('marketplace.bill_error_status'));
        }

        $orders = $offer->orders()
            ->where('market_orders.status', '=', MarketOrderStatus::Ordered)
            ->with('marketProduct')
            ->get();

        $billed = 0;

        DB::transaction(function () use ($orders, $offer, $billedBy, &$billed): void {
            foreach ($orders as $order) {
                $product = $order->marketProduct;

                if ($product?->payment_method === MarketPaymentMethod::CreditCharge && $order->totalAmount() > 0.0) {
                    $credit = new UserCredit();
                    $credit->user_id = $order->user_id;
                    $credit->market_order_id = $order->id;
                    $credit->amount = -$order->totalAmount();
                    $credit->currency = UserCredit::CURRENCY_CZK;
                    $credit->credit_type = UserCreditType::MarketplaceBilling;
                    $credit->source = UserCreditSource::User->value;
                    $credit->source_user_id = $billedBy->id;
                    $credit->status = UserCreditStatus::Done;
                    $credit->saveOrFail();

                    $note = new UserCreditNote();
                    $note->note_user_id = $billedBy->id;
                    $note->user_credit_id = $credit->id;
                    $note->note = 'Rozúčtování tržiště: '.$offer->title.' — '.$product->name
                        .' ('.$order->qty.' ks × '.number_format($order->unit_price, 2, ',', ' ').' Kč).';
                    $note->internal = true;
                    $note->params = [
                        'market_offer_id' => $offer->id,
                        'market_order_id' => $order->id,
                        'qty' => $order->qty,
                        'unit_price' => $order->unit_price,
                    ];
                    $note->saveOrFail();
                }

                $order->status = MarketOrderStatus::Billed;
                $order->saveOrFail();

                $billed++;
            }

            $offer->status = MarketOfferStatus::Billed;
            $offer->saveOrFail();
        });

        return $billed;
    }

    /**
     * Rozešle upozornění na nabídku všem aktivním členům kromě autora.
     *
     * @return int počet adresátů
     */
    public function announceOffer(MarketOffer $offer): int
    {
        $recipients = User::query()
            ->where('active', '=', 1)
            ->where('id', '!=', $offer->user_id)
            ->whereNotNull('email')
            ->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue(new MarketOfferAnnouncement($offer));
        }

        return $recipients->count();
    }

    private function notifyOfferClosed(MarketOffer $offer): void
    {
        $offer->loadMissing('user');

        $ordererIds = $offer->orders()
            ->where('market_orders.status', '!=', MarketOrderStatus::Cancelled)
            ->pluck('market_orders.user_id');

        $recipients = User::query()
            ->whereIn('id', $ordererIds->push($offer->user_id)->unique())
            ->where('active', '=', 1)
            ->whereNotNull('email')
            ->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue(new MarketOfferClosed($offer, $recipient));
        }
    }
}
