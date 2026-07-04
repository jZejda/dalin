<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Services;

use App\Enums\ServiceOrderStatus;
use App\Models\SportService;
use App\Models\SportServicePaymentDate;
use App\Models\SportServiceOrder;
use App\Models\User;
use App\Models\UserRaceProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ServiceOrderCreator
{
    public function __construct(
        private readonly OrisServiceEntryClient $orisClient = new OrisServiceEntryClient(),
    ) {
    }

    /**
     * Create a service order for the given race profile. Orders on ORIS
     * services are sent to ORIS first, internal services are stored locally only.
     */
    public function create(
        SportService $service,
        UserRaceProfile $raceProfile,
        SportServicePaymentDate $paymentDate,
        int $qty,
        ?string $note,
        User $sourceUser,
    ): ServiceOrderResult {
        if ($paymentDate->sport_service_id !== $service->id) {
            return ServiceOrderResult::failure('Zvolený termín platby nepatří k vybrané službě.');
        }

        if ($qty < 1) {
            return ServiceOrderResult::failure('Počet kusů musí být alespoň 1.');
        }

        // qty_available 0 or null means the service capacity is unlimited (ORIS convention).
        $hasLimitedCapacity = ($service->qty_available ?? 0) > 0;
        if ($hasLimitedCapacity && $qty > ($service->qty_remaining ?? 0)) {
            return ServiceOrderResult::failure('Požadovaný počet kusů překračuje volnou kapacitu služby ('.($service->qty_remaining ?? 0).').');
        }

        if (Carbon::parse($service->last_booking_date_time)->isPast()) {
            return ServiceOrderResult::failure('Termín pro objednání služby již vypršel.');
        }

        $targetUser = $raceProfile->user;
        if ($targetUser === null) {
            return ServiceOrderResult::failure('Závodní profil nemá přiřazeného uživatele.');
        }

        if (! $targetUser->canCreateEntry()) {
            return ServiceOrderResult::failure('Pro nízký stav osobního konta není možné službu objednat.');
        }

        $useOris = $this->shouldUseOris($service);
        $orisServiceEntryId = null;

        if ($useOris) {
            if ($raceProfile->club_user_id === null) {
                return ServiceOrderResult::failure('Závodní profil nemá vazbu na ORIS (chybí club user ID), službu nelze objednat.');
            }

            $orisResponse = $this->orisClient->createServiceEntry($service, $raceProfile, $qty, $note);

            if (! $orisResponse->isOk()) {
                return ServiceOrderResult::failure('ORIS objednávku odmítl: '.$orisResponse->status);
            }

            $orisServiceEntryId = $orisResponse->serviceEntryId;
        }

        $order = DB::transaction(function () use ($service, $raceProfile, $paymentDate, $qty, $note, $sourceUser, $targetUser, $orisServiceEntryId): SportServiceOrder {
            $order = new SportServiceOrder();
            $order->sport_event_id = $service->sport_event_id;
            $order->sport_service_id = $service->id;
            $order->user_id = $targetUser->id;
            $order->user_race_profile_id = $raceProfile->id;
            $order->sport_service_payment_date_id = $paymentDate->id;
            $order->qty = $qty;
            $order->unit_price = $service->unit_price;
            $order->note = $note;
            $order->oris_service_entry_id = $orisServiceEntryId;
            $order->status = ServiceOrderStatus::Ordered;
            $order->source_user_id = $sourceUser->id;
            $order->saveOrFail();

            if ($service->qty_remaining !== null) {
                $service->qty_remaining = max(0, $service->qty_remaining - $qty);
            }
            $service->qty_already_ordered = ($service->qty_already_ordered ?? 0) + $qty;
            $service->save();

            return $order;
        });

        return new ServiceOrderResult(success: true, order: $order);
    }

    private function shouldUseOris(SportService $service): bool
    {
        return $service->oris_service_id !== null
            && $service->sportEvent?->oris_id !== null;
    }
}
