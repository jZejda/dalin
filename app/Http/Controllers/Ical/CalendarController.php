<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ical;

use App\Http\Controllers\Controller;
use App\Services\IcalService;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group("Calendar", "APIs for server ical calendar feed common calendar applications")]
#[Subgroup("ICAL", "Server ical calendar feed")]
class CalendarController extends Controller
{
    private IcalService $icalService;

    public function __construct(?IcalService $icalService)
    {
        $this->icalService = $icalService ?? new IcalService();
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
}
