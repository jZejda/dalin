<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ical;

use App\Http\Controllers\Controller;
use App\Services\CalendarTokenService;
use App\Services\IcalService;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group("Calendar", "APIs for server ical calendar feed common calendar applications")]
#[Subgroup("ICAL", "Server ical calendar feed")]
class CalendarController extends Controller
{
    public function __construct(
        private readonly IcalService $icalService,
        private readonly CalendarTokenService $calendarTokenService,
    ) {
    }

    public function raceCalendar(): Response
    {
        return response($this->icalService->getRaceCalendar()->get())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="abm-zavody.ics"');
    }

    public function trainingCalendar(): Response
    {
        return response($this->icalService->getTrainingCalendar()->get())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="abm-treninky.ics"');
    }

    public function personalRaceCalendar(string $token): Response
    {
        $user = $this->calendarTokenService->validate($token);

        if ($user === null) {
            abort(404);
        }

        return response($this->icalService->getPersonalRaceCalendar($user)->get())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="abm-moje-zavody.ics"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function personalTrainingCalendar(string $token): Response
    {
        $user = $this->calendarTokenService->validate($token);

        if ($user === null) {
            abort(404);
        }

        return response($this->icalService->getPersonalTrainingCalendar($user)->get())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="abm-moje-treninky.ics"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
