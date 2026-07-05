<?php

declare(strict_types=1);

namespace App\Mail;

use App\Filament\Pages\MarketplaceList;
use App\Models\MarketOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarketOfferAnnouncement extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly MarketOffer $offer,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' | '.__('marketplace.mail.announcement_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.marketplace.offerAnnouncement',
            with: [
                'offer' => $this->offer->loadMissing(['user', 'products']),
                'marketplaceUrl' => MarketplaceList::getUrl(panel: 'admin'),
            ],
        );
    }
}
