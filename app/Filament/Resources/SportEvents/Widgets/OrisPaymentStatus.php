<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Widgets;

use App\Enums\AppRoles;
use App\Http\Components\Oris\Response\Entity\EventClubBalance;
use App\Models\SportEvent;
use App\Services\Mail\MailMoney;
use App\Services\OrisApiService;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Override;

/**
 * Club's entry-fee settlement of the event as recorded by the organizer in ORIS (getEventBalance).
 */
class OrisPaymentStatus extends StatsOverviewWidget
{
    public const int CACHE_TTL_SECONDS = 600;

    public ?Model $record = null;

    // ORIS is an external API — never poll it from an open page
    protected ?string $pollingInterval = null;

    private bool $loaded = false;

    private ?EventClubBalance $balance = null;

    private bool $unavailable = false;

    private ?Carbon $fetchedAt = null;

    #[Override]
    public static function canView(): bool
    {
        return auth()->user()?->hasRole([
            AppRoles::SuperAdmin->value,
            AppRoles::ClubAdmin->value,
            AppRoles::EventMaster->value,
            AppRoles::BillingSpecialist->value,
        ]) ?? false;
    }

    public static function cacheKey(int $orisEventId): string
    {
        return 'oris.event-club-balance.'.$orisEventId;
    }

    #[Override]
    protected function getHeading(): string
    {
        return __('sport-event.widgets.oris_payment.heading');
    }

    #[Override]
    protected function getDescription(): ?string
    {
        $this->load();

        if ($this->unavailable) {
            return __('sport-event.widgets.oris_payment.unavailable');
        }

        if ($this->balance === null) {
            return __('sport-event.widgets.oris_payment.no_entries');
        }

        return __('sport-event.widgets.oris_payment.description', [
            'time' => $this->fetchedAt?->format('j. n. Y H:i') ?? '',
        ]);
    }

    /**
     * @return array<Stat>
     */
    #[Override]
    protected function getStats(): array
    {
        $this->load();

        $balance = $this->balance;
        if ($balance === null) {
            return [];
        }

        $money = fn (float $amount): string => MailMoney::format($amount, $balance->currency);

        $toBePaid = Stat::make(__('sport-event.widgets.oris_payment.to_be_paid'), $money(max($balance->toBePaid, 0)));
        $toBePaid = match (true) {
            $balance->toBePaid > 0 => $toBePaid
                ->description(__('sport-event.widgets.oris_payment.status_unpaid'))
                ->descriptionIcon(LucideIcon::CircleAlert)
                ->color('danger'),
            $balance->toBePaid < 0 => $toBePaid
                ->description(__('sport-event.widgets.oris_payment.status_overpaid', ['amount' => $money(-$balance->toBePaid)]))
                ->descriptionIcon(LucideIcon::CircleCheck)
                ->color('warning'),
            default => $toBePaid
                ->description(__('sport-event.widgets.oris_payment.status_paid'))
                ->descriptionIcon(LucideIcon::CircleCheck)
                ->color('success'),
        };

        $paid = Stat::make(__('sport-event.widgets.oris_payment.paid'), $money($balance->paid))
            ->icon(LucideIcon::BanknoteCheck);
        if ($balance->paymentVs !== null) {
            $paid->description(__('sport-event.widgets.oris_payment.payment_vs', ['vs' => $balance->paymentVs]));
        }

        return [
            Stat::make(__('sport-event.widgets.oris_payment.fee_total'), $money($balance->feeTotal))
                ->description(__('sport-event.widgets.oris_payment.fee_breakdown', [
                    'entry' => $money($balance->feeEntry),
                    'si' => $money($balance->feeSi),
                    'service' => $money($balance->feeService),
                ]))
                ->icon(LucideIcon::Receipt),
            $paid,
            $toBePaid,
        ];
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;

        $orisId = $this->record instanceof SportEvent ? $this->record->oris_id : null;
        if ($orisId === null) {
            $this->unavailable = true;

            return;
        }

        try {
            // Wrapped in an array so "club has no entries" (null) is cached too
            $cached = Cache::remember(self::cacheKey($orisId), self::CACHE_TTL_SECONDS, fn (): array => [
                'balance' => (new OrisApiService())
                    ->getEventClubBalance($orisId, (string) config('site-config.club.abbr'))
                    ?->toArray(),
                'fetched_at' => now()->toIso8601String(),
            ]);
        } catch (HttpClientException $e) {
            Log::channel('site')->warning('ORIS getEventBalance failed: '.$e->getMessage());
            $this->unavailable = true;

            return;
        }

        $this->balance = is_array($cached['balance']) ? EventClubBalance::fromArray($cached['balance']) : null;
        $this->fetchedAt = Carbon::parse($cached['fetched_at']);
    }
}
