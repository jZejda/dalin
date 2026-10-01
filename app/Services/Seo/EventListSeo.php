<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Http\Components\Iofv3\Entities\ClassResult;
use App\Http\Components\Iofv3\Entities\ClassStart;
use App\Http\Components\Iofv3\Entities\Person;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use App\Shared\Helpers\AppHelper;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * Search/social metadata of the IOF start list (/startovka/{slug}) and result list
 * (/vysledky/{slug}) pages: "Startovka – <event>" plus date, place and how many
 * classes / runners the list holds, and breadcrumbs under the event detail.
 */
final class EventListSeo
{
    private const string START_LIST = 'start_list';

    private const string RESULT_LIST = 'result_list';

    public function __construct(
        private readonly SiteSeo $siteSeo,
        private readonly EventSeo $eventSeo,
    ) {
    }

    /**
     * @param  array<ClassStart>|null  $classes
     */
    public function startList(?SportEventExport $export, ?string $eventName, ?array $classes): SEOData
    {
        $runners = 0;
        foreach ($classes ?? [] as $class) {
            foreach ($class->getPersonStart() as $personStart) {
                $runners += $this->isVacancy($personStart->getPerson()) ? 0 : 1;
            }
        }

        return $this->build(self::START_LIST, $export, $eventName, count($classes ?? []), $runners);
    }

    /**
     * @param  array<ClassResult>|null  $classes
     */
    public function resultList(?SportEventExport $export, ?string $eventName, ?array $classes): SEOData
    {
        $runners = 0;
        foreach ($classes ?? [] as $class) {
            foreach ($class->getPersonResult() as $personResult) {
                $runners += $this->isVacancy($personResult->getPerson()) ? 0 : 1;
            }
        }

        return $this->build(self::RESULT_LIST, $export, $eventName, count($classes ?? []), $runners);
    }

    private function build(string $kind, ?SportEventExport $export, ?string $eventName, int $classCount, int $runnerCount): SEOData
    {
        $event = $export?->sport_event_id !== null ? SportEvent::query()->find($export->sport_event_id) : null;
        $name = $this->firstFilled([$eventName, $event?->name, $export?->title]);

        if ($name !== null) {
            $title = __('sport-event-export.seo.'.$kind.'_title', ['event' => $name]);
        } else {
            $title = __('sport-event-export.seo.'.$kind.'_title_fallback');
        }

        $facts = array_filter([
            $event?->date?->format(AppHelper::DATE_FORMAT),
            $event !== null ? $this->eventSeo->placeName($event) : null,
            $classCount > 0 ? trans_choice('sport-event-export.seo.classes', $classCount) : null,
            $runnerCount > 0 ? trans_choice('sport-event-export.seo.runners', $runnerCount) : null,
        ], static fn (?string $fact): bool => $fact !== null && $fact !== '');

        $url = url()->current();
        $breadcrumbs = [];

        if ($event !== null) {
            $breadcrumbs[] = ['name' => $event->name, 'url' => route('sport-event.show', $event->id)];
        }

        $breadcrumbs[] = ['name' => $title, 'url' => $url];

        return new SEOData(
            title: $title,
            description: $facts !== [] ? $this->siteSeo->description(e(implode(' · ', $facts))) : null,
            url: $url,
            schema: SchemaCollection::make()->add(fn (): array => $this->siteSeo->breadcrumbSchema($breadcrumbs)),
        );
    }

    /**
     * Placeholder entries the organizer leaves for late entries (same check as the views).
     */
    private function isVacancy(Person $person): bool
    {
        return $person->getName()->getFamily() === 'Vakant' || $person->getName()->getGiven() === 'Vakant';
    }

    /**
     * @param  list<string|null>  $values
     */
    private function firstFilled(array $values): ?string
    {
        foreach ($values as $value) {
            if (($filled = SiteSeo::filled($value)) !== null) {
                return $filled;
            }
        }

        return null;
    }
}
