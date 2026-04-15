<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\SportEventType;
use App\Http\Controllers\Controller;
use App\Models\SportClass;
use App\Models\SportEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('V1', 'APIs V1')]
#[Subgroup('SPORT EVENT', 'Sport events and event category options')]
final class SportEventController extends Controller
{
    #[QueryParam('from', 'string', description: 'Date from in Y-m-d format.', required: false, example: '2026-01-01')]
    #[QueryParam('to', 'string', description: 'Date to in Y-m-d format.', required: false, example: '2026-12-31')]
    #[QueryParam('event_type', 'string', description: 'Filter by event type category. Allowed values: race, training, trainingCamp, other.', required: false, example: 'race')]
    #[QueryParam('class_definition_id', 'integer', description: 'Filter events by category/class definition ID assigned to the event.', required: false, example: 15)]
    #[QueryParam('page', 'integer', description: 'Page number for pagination.', required: false, example: 1)]
    #[QueryParam('per_page', 'integer', description: 'Number of items per page. Defaults to 20.', required: false, example: 20)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/sport-event.list.json', 200, description: 'Example Sport Event List')]
    public function list(Request $request): JsonResource
    {
        $query = SportEvent::query()
            ->with(['sportClasses.classDefinition']);

        $from = $request->query('from');
        $to = $request->query('to');
        $eventType = $request->query('event_type');
        $classDefinitionId = $request->query('class_definition_id');

        if ($from !== null) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $query->where('date', '>=', $fromDate);
        }

        if ($to !== null) {
            $toDate = Carbon::parse($to)->endOfDay();
            $query->where('date', '<=', $toDate);
        }

        if (is_string($eventType) && SportEventType::tryFrom($eventType) !== null) {
            $query->where('event_type', $eventType);
        }

        if ($classDefinitionId !== null) {
            $query->whereHas('sportClasses', static function ($sportClassQuery) use ($classDefinitionId): void {
                $sportClassQuery->where('class_definition_id', (int) $classDefinitionId);
            });
        }

        $sportEvents = $query
            ->orderByDesc('date')
            ->select([
                'id',
                'name',
                'date',
                'entry_date_1',
                'event_type',
                'created_at',
                'updated_at',
            ])
            ->simplePaginate((int) $request->query('per_page', 20));

        $sportEvents->getCollection()->transform(static function (SportEvent $sportEvent): array {
            /** @var list<array{id: int, name: string|null}> $categories */
            $categories = $sportEvent->sportClasses
                ->map(static fn (SportClass $sportClass): array => [
                    'id' => $sportClass->class_definition_id,
                    'name' => $sportClass->classDefinition?->name,
                ])
                ->unique('id')
                ->values()
                ->all();

            return [
                'id' => $sportEvent->id,
                'name' => $sportEvent->name,
                'date' => $sportEvent->date?->toDateString(),
                'entry_date' => $sportEvent->entry_date_1?->format('Y-m-d H:i:s'),
                'event_type' => [
                    'value' => $sportEvent->event_type?->value,
                    'label' => $sportEvent->event_type !== null ? __('sport-event.type_enum.'.$sportEvent->event_type->value) : null,
                ],
                'categories' => $categories,
                'created_at' => $sportEvent->created_at,
                'updated_at' => $sportEvent->updated_at,
            ];
        });

        return JsonResource::collection($sportEvents)->additional([
            'components' => [
                'event_type_options' => SportEventType::enumArray(),
                'category_component_description' => 'categories contains class definitions assigned to each event (SportClass -> SportClassDefinition).',
                'event_type_component_description' => 'event_type contains both machine value and translated label.',
            ],
        ]);
    }
}
