<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\MarketOrderStatus;
use App\Models\MarketOffer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarketOfferClosed extends Mailable
{
    use Queueable;
    use SerializesModels;

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
        $recipientOrders = $this->offer->orders()
            ->where('market_orders.user_id', '=', $this->recipient->id)
            ->where('market_orders.status', '!=', MarketOrderStatus::Cancelled)
            ->with('marketProduct')
            ->get();

        return new Content(
            markdown: 'emails.marketplace.offerClosed',
            with: [
                'offer' => $this->offer,
                'recipient' => $this->recipient,
                'recipientOrders' => $recipientOrders,
                'isAuthor' => $this->offer->user_id === $this->recipient->id,
            ],
        );
    }
}
