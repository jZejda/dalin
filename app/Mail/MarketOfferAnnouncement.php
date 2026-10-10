<?php

declare(strict_types=1);

namespace App\Mail;

use App\Filament\Pages\MarketplaceList;
use App\Models\MarketOffer;
use App\Models\MarketProduct;
use App\Services\Mail\MailMoney;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A new marketplace offer announced to all active members except its author.
 */
class MarketOfferAnnouncement extends Mailable
{
    use Queueable;
    use SerializesModels;

    private const string DATE_TIME_FORMAT = 'j. n. Y · H:i';

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

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
        $offer = $this->offer->loadMissing(['user', 'products']);

        return new Content(
            markdown: 'emails.marketplace.offerAnnouncement',
            with: [
                'isClubOffer' => $offer->is_club_offer,
                'title' => $offer->title,
                'author' => (string) $offer->user?->name,
                'description' => $offer->description,
                'closesAt' => $offer->closes_at->format(self::DATE_TIME_FORMAT),
                'products' => $offer->products->map(static fn (MarketProduct $product): array => [
                    'name' => $product->name,
                    'detail' => self::productDetail($product),
                    'price' => $product->isFree() ? __('marketplace.mail.club.free') : MailMoney::format($product->unit_price),
                ])->values()->all(),
                'marketplaceUrl' => MarketplaceList::getUrl(panel: 'admin'),
            ],
        );
    }

    private static function productDetail(MarketProduct $product): string
    {
        $availability = $product->hasUnlimitedQty()
            ? __('marketplace.mail.club.qty_unlimited')
            : __('marketplace.mail.club.qty_available', ['qty' => $product->qty_available]);

        if ($product->isFree()) {
            return ucfirst($availability);
        }

        return __('marketplace.mail.club.per_unit').' · '.$availability;
    }
}
