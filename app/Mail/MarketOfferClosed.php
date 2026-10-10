<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\MarketOrderStatus;
use App\Enums\MarketPaymentMethod;
use App\Models\MarketOffer;
use App\Models\MarketOrder;
use App\Models\User;
use App\Services\Mail\MailMoney;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A closed marketplace offer: buyers get their orders and how they'll pay, the author
 * gets the next steps (plus their own orders, if any).
 */
class MarketOfferClosed extends Mailable
{
    use Queueable;
    use SerializesModels;

    private const string DATE_TIME_FORMAT = 'j. n. Y · H:i';

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    public function __construct(
        private readonly MarketOffer $offer,
        private readonly User $recipient,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' | '.__('marketplace.mail.offer_closed_subject'),
        );
    }

    public function content(): Content
    {
        $orders = $this->offer->orders()
            ->where('market_orders.user_id', '=', $this->recipient->id)
            ->where('market_orders.status', '!=', MarketOrderStatus::Cancelled)
            ->with('marketProduct')
            ->get();

        $paidMethods = $orders
            ->filter(static fn (MarketOrder $order): bool => $order->unit_price > 0)
            ->map(static fn (MarketOrder $order): ?MarketPaymentMethod => $order->marketProduct?->payment_method)
            ->filter()
            ->unique();

        return new Content(
            markdown: 'emails.marketplace.offerClosed',
            with: [
                'isAuthor' => $this->offer->user_id === $this->recipient->id,
                'title' => $this->offer->title,
                'author' => (string) $this->offer->user?->name,
                'closedAt' => ($this->offer->closed_at ?? $this->offer->closes_at)->format(self::DATE_TIME_FORMAT),
                'orders' => $orders->map(static fn (MarketOrder $order): array => self::orderRow($order))->values()->all(),
                'payByCredit' => $paidMethods->contains(MarketPaymentMethod::CreditCharge),
                'payDirectly' => $paidMethods->contains(MarketPaymentMethod::DirectPayment),
            ],
        );
    }

    /**
     * @return array{name: string, detail: string, amount: string}
     */
    private static function orderRow(MarketOrder $order): array
    {
        $name = (string) $order->marketProduct?->name.' · '.__('marketplace.mail.qty_suffix', ['qty' => $order->qty]);

        if ($order->unit_price <= 0) {
            return ['name' => $name, 'detail' => __('marketplace.mail.club.closed.no_payment'), 'amount' => __('marketplace.mail.club.free')];
        }

        $method = $order->marketProduct?->payment_method === MarketPaymentMethod::DirectPayment
            ? __('marketplace.mail.club.closed.pay_direct')
            : __('marketplace.mail.club.closed.pay_credit');

        return [
            'name' => $name,
            'detail' => __('marketplace.mail.club.closed.unit_price', ['price' => MailMoney::format($order->unit_price)]).' · '.$method,
            'amount' => MailMoney::format($order->totalAmount()),
        ];
    }
}
