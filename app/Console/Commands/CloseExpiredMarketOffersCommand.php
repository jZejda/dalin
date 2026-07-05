<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\MarketOfferStatus;
use App\Models\AppSetting;
use App\Models\MarketOffer;
use App\Services\MarketplaceService;
use Illuminate\Console\Command;

class CloseExpiredMarketOffersCommand extends Command
{
    protected $signature = 'marketplace:close-expired';

    protected $description = 'Uzavře nabídky tržiště s prošlým termínem a rozešle e-mail zúčastněným.';

    public function handle(MarketplaceService $marketplaceService): int
    {
        if (! AppSetting::isMarketplaceModuleEnabled()) {
            $this->info('Modul tržiště je vypnutý, nic se neuzavírá.');

            return self::SUCCESS;
        }

        $expiredOffers = MarketOffer::query()
            ->where('status', '=', MarketOfferStatus::Active)
            ->where('closes_at', '<=', now())
            ->get();

        foreach ($expiredOffers as $offer) {
            $marketplaceService->closeOffer($offer);
        }

        $this->info(sprintf('Uzavřeno nabídek: %d', $expiredOffers->count()));

        return self::SUCCESS;
    }
}
