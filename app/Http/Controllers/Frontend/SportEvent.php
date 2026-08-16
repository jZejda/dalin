<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SportEvent as ModelsSportEvent;
use App\Models\TransportOffer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class SportEvent extends Controller
{
    public function singleEvent(string|null $eventId): View
    {
        try {
            $event = ModelsSportEvent::query()
                ->with([
                    'sportDiscipline',
                    'sportLevel',
                    'sportClasses',
                    'sportServices',
                    'sportEventLinks',
                    'sportEventMarkers',
                    'sportEventNews',
                ])
                ->findOrFail($eventId);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }

        $transportOffers = TransportOffer::query()
            ->forEvent($event->id)
            ->active()
            ->with(['requests'])
            ->get();

        return view('pages.frontend.single-event', [
            'event' => $event,
            'activeEntriesCount' => $event->userEntryActive(),
            'transportOffersCount' => $transportOffers->count(),
            'transportFreeSeats' => (int) $transportOffers->sum(fn (TransportOffer $offer): int => $offer->freeSeats()),
            'sponsorSectionId' => 0,
        ]);
    }
}
