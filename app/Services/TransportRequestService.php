<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use App\Mail\TransportOfferCancelled;
use App\Mail\TransportRequestCancelled;
use App\Mail\TransportRequestCreated;
use App\Mail\TransportRequestDecided;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class TransportRequestService
{
    /**
     * Vytvoří žádost o místo (pending) a pošle řidiči e-mail
     * se schvalovacími linky.
     */
    public function create(TransportOffer $offer, User $requester, TransportDirection $direction, int $seats): TransportRequest
    {
        $transportRequest = TransportRequest::query()->create([
            'transport_offer_id' => $offer->id,
            'user_id' => $requester->id,
            'direction' => $direction,
            'seats' => $seats,
            'status' => TransportRequestStatus::Pending,
        ]);

        $driver = $offer->user;
        if ($driver !== null) {
            Mail::to($driver)->queue(new TransportRequestCreated(
                $transportRequest,
                $this->signedDecisionUrl($transportRequest, 'approve'),
                $this->signedDecisionUrl($transportRequest, 'reject'),
            ));
        }

        return $transportRequest;
    }

    /**
     * Schválí žádost. Kapacita se kontroluje v transakci se zámkem nabídky,
     * při nedostatku míst se žádost zamítne. Vrací true při schválení.
     */
    public function approve(TransportRequest $transportRequest): bool
    {
        if ($transportRequest->isApproved()) {
            return true;
        }

        if (! $transportRequest->isPending()) {
            return false;
        }

        $approved = DB::transaction(function () use ($transportRequest): bool {
            /** @var TransportOffer|null $offer */
            $offer = TransportOffer::query()
                ->lockForUpdate()
                ->find($transportRequest->transport_offer_id);

            if ($offer === null) {
                return false;
            }

            $offer->load('requests');

            if ($offer->freeSeatsFor($transportRequest->direction) < $transportRequest->seats) {
                $transportRequest->update(['status' => TransportRequestStatus::Rejected]);

                return false;
            }

            $transportRequest->update([
                'status' => TransportRequestStatus::Approved,
                'approved_at' => Carbon::now(),
            ]);

            return true;
        });

        $this->notifyRequester($transportRequest->refresh());

        return $approved;
    }

    /**
     * Zamítne čekající žádost a informuje žadatele.
     */
    public function reject(TransportRequest $transportRequest): bool
    {
        if (! $transportRequest->isPending()) {
            return false;
        }

        $transportRequest->update(['status' => TransportRequestStatus::Rejected]);
        $this->notifyRequester($transportRequest);

        return true;
    }

    /**
     * Zruší žádost ze strany žadatele; u schválené žádosti se uvolní
     * místa a řidič dostane e-mail.
     */
    public function cancel(TransportRequest $transportRequest): bool
    {
        if (! $transportRequest->isPending() && ! $transportRequest->isApproved()) {
            return false;
        }

        $wasApproved = $transportRequest->isApproved();
        $transportRequest->update(['status' => TransportRequestStatus::Cancelled]);

        $driver = $transportRequest->transportOffer?->user;
        if ($wasApproved && $driver !== null) {
            Mail::to($driver)->queue(new TransportRequestCancelled($transportRequest));
        }

        return true;
    }

    /**
     * Při zrušení/smazání nabídky zruší všechny otevřené žádosti
     * a informuje žadatele e-mailem.
     */
    public function cancelOffer(TransportOffer $offer): void
    {
        $openRequests = $offer->requests()
            ->whereIn('status', [TransportRequestStatus::Pending->value, TransportRequestStatus::Approved->value])
            ->get();

        foreach ($openRequests as $openRequest) {
            $openRequest->update(['status' => TransportRequestStatus::Cancelled]);

            $requester = $openRequest->user;
            if ($requester !== null) {
                Mail::to($requester)->queue(new TransportOfferCancelled($openRequest));
            }
        }
    }

    private function notifyRequester(TransportRequest $transportRequest): void
    {
        $requester = $transportRequest->user;
        if ($requester !== null) {
            Mail::to($requester)->queue(new TransportRequestDecided($transportRequest));
        }
    }

    /**
     * Podepsaný link platí do dne konání závodu, nejméně však den
     * a nejdéle 30 dní od vytvoření žádosti.
     */
    private function signedDecisionUrl(TransportRequest $transportRequest, string $decision): string
    {
        $eventDate = $transportRequest->transportOffer?->sportEvent?->date?->endOfDay();

        $expiresAt = $eventDate ?? Carbon::now()->addDays(14);
        $expiresAt = $expiresAt->min(Carbon::now()->addDays(30))->max(Carbon::now()->addDay());

        return URL::temporarySignedRoute('transport-request.decision', $expiresAt, [
            'transportRequest' => $transportRequest->id,
            'decision' => $decision,
        ]);
    }
}
