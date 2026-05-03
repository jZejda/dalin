<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EntryStatus;
use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\Classification;

final class IcalService
{
    public function getRaceCalendar(): Calendar
    {
        return Calendar::create()

            ->name(config('site-config.club.abbr').' - Kalendář závodů')
            ->description('Kalendař závodů na tento a následujici rok.')
            ->event($this->getEvents(SportEventType::Race));
    }

    public function getTrainingCalendar(): Calendar
    {
        return Calendar::create()

            ->name(config('site-config.club.abbr').' - Kalendář tréninků')
            ->description('Kalendař tréninků na tento a následujici rok.')
            ->event($this->getEvents(SportEventType::Training));
    }

    public function getPersonalRaceCalendar(User $user): Calendar
    {
        return Calendar::create()
            ->name(config('site-config.club.abbr').' - Moje závody')
            ->description('Kalendař závodů na tento a následujici rok — pouze akce kde jsi přihlášen/a.')
            ->event($this->getEvents(SportEventType::Race, $user));
    }

    public function getPersonalTrainingCalendar(User $user): Calendar
    {
        return Calendar::create()
            ->name(config('site-config.club.abbr').' - Moje tréninky')
            ->description('Kalendař tréninků na tento a následujici rok — pouze akce kde jsi přihlášen/a.')
            ->event($this->getEvents(SportEventType::Training, $user));
    }

    /**
     * @return Event[]
     */
    public function getEvents(SportEventType $type, ?User $user = null): array
    {
        $icalEvents = [];

        /** @var Collection<int, SportEvent> $sportEvents */
        $sportEvents = $user !== null
            ? $this->getEventsByUser($user, $type)
            : $this->getEventByType($type);

        foreach ($sportEvents as $sportEvent) {

            $fullDay = false;

            $startTime = $sportEvent->start_time;
            if ($startTime === '00:00:00' || $startTime === null) {
                $startTime = Carbon::now()->startOfDay()->addHours(10);
            }

            $dateFrom = $sportEvent->date?->setTimeFrom($startTime);
            $dateEnd = $sportEvent->date?->setTimeFrom($startTime)->addHours(4);

            if ($sportEvent->alt_name !== null) {
                $name = $sportEvent->alt_name;
                $description = $sportEvent->name;
            } else {
                $name = $sportEvent->name;
                $description = null;
            }

            if ($description !== null) {
                $description .= ' | ';
            }
            $description .= $this->addToDescription($sportEvent->place, ' ');

            if ($sportEvent->organization !== null) {
                $description .= 'Organizátor: ';
                foreach ($sportEvent->organization as $organization) {
                    $description .= $this->addToDescription($organization);
                }
            }

            if ($sportEvent->region !== null) {
                $description .= 'Region: ';
                foreach ($sportEvent->region as $region) {
                    $description .= $this->addToDescription($region);
                }
            }

            if ($sportEvent->date_end !== null && $sportEvent->stages > 1) {
                $fullDay = true;
                $dateFrom = $sportEvent->date?->startOfDay();
                $dateEnd = $sportEvent->date_end->endOfDay();
            }

            $event = Event::create()
                ->description(trim($description))
                ->name($name)
                ->uniqueIdentifier($sportEvent->oris_id !== null ? (string) $sportEvent->oris_id : (string) $sportEvent->id)
                ->createdAt($sportEvent->date)
                ->startsAt($dateFrom)
                ->endsAt($dateEnd)
                ->address($sportEvent->place ?? 'N/A')
                ->classification(Classification::public())
                ->url(route('filament.admin.resources.sport-events.entry', ['record' => $sportEvent->id]));

            if ($sportEvent->gps_lat !== null && $sportEvent->gps_lon !== null) {
                $event->coordinates((float) $sportEvent->gps_lat, (float) $sportEvent->gps_lon);
            }

            if ($fullDay) {
                $event->fullDay();
            }

            $icalEvents[] = $event;
        }

        return $icalEvents;
    }

    private function getEventByType(SportEventType $type): Collection
    {
        return SportEvent::query()
            ->where('event_type', '=', $type)
            ->where('date', '>', Carbon::now()->startOfYear())
            ->where('date', '<', Carbon::now()->endOfYear()->addYear())
            ->where('cancelled', false)
            ->get();
    }

    /**
     * @return Collection<int, SportEvent>
     */
    private function getEventsByUser(User $user, SportEventType $type): Collection
    {
        $profileIds = $user->userRaceProfiles()->pluck('id');
        $sportEventIds = UserEntry::query()
            ->whereIn('user_race_profile_id', $profileIds)
            ->where('entry_status', '!=', EntryStatus::Cancel)
            ->pluck('sport_event_id');

        return SportEvent::query()
            ->whereIn('id', $sportEventIds)
            ->where('event_type', '=', $type)
            ->where('date', '>', Carbon::now()->startOfYear())
            ->where('date', '<', Carbon::now()->endOfYear()->addYear())
            ->where('cancelled', false)
            ->get();
    }

    private function addToDescription(?string $item, string $delimiter = ' - '): string
    {
        if ($item !== null) {
            return $item.$delimiter;
        }

        return '';
    }
}
