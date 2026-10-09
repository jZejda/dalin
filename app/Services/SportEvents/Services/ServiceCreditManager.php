<?php

declare(strict_types=1);

namespace App\Services\SportEvents\Services;

use App\Enums\ServiceOrderStatus;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\SportService;
use App\Models\SportServiceOrder;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Models\UserRaceProfile;
use Illuminate\Support\Facades\DB;

/**
 * Charges an additional service of a sport event directly to race profiles
 * (bypassing service orders / ORIS) and reverses such charges.
 *
 * The credit journal is append-only: removing a service never deletes or edits
 * the original charge, it books a reversal entry with the opposite amount.
 */
class ServiceCreditManager
{
    /**
     * @param  iterable<UserRaceProfile>  $raceProfiles
     * @return int number of created charges
     */
    public function assign(SportService $service, iterable $raceProfiles, int $qty, ?string $note, User $assignedBy): int
    {
        $unitPrice = (float) $service->unit_price;
        $amount = round($qty * $unitPrice, 2);
        $created = 0;

        DB::transaction(function () use ($service, $raceProfiles, $qty, $note, $assignedBy, $unitPrice, $amount, &$created): void {
            foreach ($raceProfiles as $raceProfile) {
                $credit = $this->createCredit($service, $raceProfile, -$amount, $assignedBy);

                $this->addInternalNote(
                    $credit,
                    $assignedBy,
                    'Přiřazení doplňkové služby: '.$this->serviceName($service)
                        .' ('.$qty.' ks × '.number_format($unitPrice, 2, ',', ' ').' Kč).',
                    ['qty' => $qty, 'unit_price' => $unitPrice],
                );
                $this->addPublicNote($credit, $assignedBy, $note);

                $created++;
            }
        });

        return $created;
    }

    /**
     * Books a reversal of qty units of the service for every profile that is
     * charged for it; the reversal never exceeds the outstanding charge.
     * Profiles without a charge are skipped. When the charge of a profile is
     * fully reversed, its billed service orders are marked as cancelled.
     *
     * @param  iterable<UserRaceProfile>  $raceProfiles
     * @return int number of created reversals
     */
    public function reverse(SportService $service, iterable $raceProfiles, int $qty, ?string $note, User $reversedBy): int
    {
        $unitPrice = (float) $service->unit_price;
        $requested = round($qty * $unitPrice, 2);
        $reversed = 0;

        DB::transaction(function () use ($service, $raceProfiles, $qty, $note, $reversedBy, $unitPrice, $requested, &$reversed): void {
            foreach ($raceProfiles as $raceProfile) {
                $charged = $this->chargedAmount($service, $raceProfile);
                $amount = min($requested, $charged);
                if ($amount <= 0.0) {
                    continue;
                }

                $credit = $this->createCredit($service, $raceProfile, $amount, $reversedBy);

                $cancelledOrderIds = $amount >= $charged
                    ? $this->cancelBilledOrders($service, $raceProfile)
                    : [];

                $this->addInternalNote(
                    $credit,
                    $reversedBy,
                    'Storno doplňkové služby: '.$this->serviceName($service)
                        .' ('.$qty.' ks × '.number_format($unitPrice, 2, ',', ' ').' Kč, vráceno '
                        .number_format($amount, 2, ',', ' ').' Kč).',
                    [
                        'reversal' => true,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'reversed_amount' => $amount,
                        'cancelled_service_order_ids' => $cancelledOrderIds,
                    ],
                );
                $this->addPublicNote($credit, $reversedBy, $note);

                $reversed++;
            }
        });

        return $reversed;
    }

    /**
     * Outstanding (not yet reversed) amount the profile was charged for the service.
     * Positive number means the profile currently pays for the service.
     */
    public function chargedAmount(SportService $service, UserRaceProfile $raceProfile): float
    {
        $net = (float) UserCredit::query()
            ->where('sport_event_id', '=', $service->sport_event_id)
            ->where('sport_service_id', '=', $service->id)
            ->where('user_race_profile_id', '=', $raceProfile->id)
            ->sum('amount');

        return round(-$net, 2);
    }

    /**
     * Services of the event that at least one of the given profiles is currently charged for.
     *
     * @param  array<int, int>  $raceProfileIds
     * @return list<int>
     */
    public function chargedServiceIds(int $sportEventId, array $raceProfileIds): array
    {
        if ($raceProfileIds === []) {
            return [];
        }

        /** @var list<int> $ids */
        $ids = UserCredit::query()
            ->select('sport_service_id')
            ->where('sport_event_id', '=', $sportEventId)
            ->whereNotNull('sport_service_id')
            ->whereIn('user_race_profile_id', $raceProfileIds)
            ->groupBy('sport_service_id', 'user_race_profile_id')
            ->havingRaw('ROUND(SUM(amount), 2) < 0')
            ->pluck('sport_service_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        return $ids;
    }

    /**
     * @return list<int> ids of the cancelled orders
     */
    private function cancelBilledOrders(SportService $service, UserRaceProfile $raceProfile): array
    {
        $orders = SportServiceOrder::query()
            ->where('sport_service_id', '=', $service->id)
            ->where('user_race_profile_id', '=', $raceProfile->id)
            ->where('status', '=', ServiceOrderStatus::Billed->value)
            ->get();

        $canceller = new ServiceOrderCanceller();
        $ids = [];

        foreach ($orders as $order) {
            $order->status = ServiceOrderStatus::Cancelled;
            $order->saveOrFail();
            $canceller->releaseCapacity($order);
            $ids[] = $order->id;
        }

        return $ids;
    }

    private function createCredit(SportService $service, UserRaceProfile $raceProfile, float $amount, User $sourceUser): UserCredit
    {
        $credit = new UserCredit();
        $credit->user_id = $raceProfile->user_id;
        $credit->user_race_profile_id = $raceProfile->id;
        $credit->sport_event_id = $service->sport_event_id;
        $credit->sport_service_id = $service->id;
        $credit->amount = $amount;
        $credit->currency = UserCredit::CURRENCY_CZK;
        $credit->credit_type = UserCreditType::ServiceFee;
        $credit->source = UserCreditSource::User->value;
        $credit->source_user_id = $sourceUser->id;
        $credit->status = UserCreditStatus::Done;
        $credit->saveOrFail();

        return $credit;
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function addInternalNote(UserCredit $credit, User $author, string $text, array $params): void
    {
        $note = new UserCreditNote();
        $note->user_credit_id = $credit->id;
        $note->note_user_id = $author->id;
        $note->note = $text;
        $note->internal = true;
        $note->params = $params;
        $note->saveOrFail();
    }

    private function addPublicNote(UserCredit $credit, User $author, ?string $text): void
    {
        if ($text === null || trim($text) === '') {
            return;
        }

        $note = new UserCreditNote();
        $note->user_credit_id = $credit->id;
        $note->note_user_id = $author->id;
        $note->note = $text;
        $note->internal = false;
        $note->saveOrFail();
    }

    private function serviceName(SportService $service): string
    {
        return $service->service_name_cz ?? '#'.$service->id;
    }
}
