<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\SportEventType;
use App\Http\Controllers\Controller;
use App\Models\RelayTeam;
use App\Models\RelayTeamMember;
use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\SportEventLink;
use App\Models\SportService;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;

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
    public function list(Request $request): JsonResponse
    {
        $request->validate([
            'from'                => ['nullable', 'date_format:Y-m-d'],
            'to'                  => ['nullable', 'date_format:Y-m-d'],
            'event_type'          => ['nullable', Rule::in(array_column(SportEventType::cases(), 'value'))],
            'class_definition_id' => ['nullable', 'integer'],
            'per_page'            => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

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
                'oris_id',
                'use_oris_for_entries',
                'cancelled',
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
                'oris_id' => $sportEvent->oris_id,
                'use_oris_for_entries' => $sportEvent->use_oris_for_entries,
                'cancelled' => $sportEvent->cancelled,
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
        ])->response();
    }

    #[UrlParam('sportEvent', 'integer', description: 'Sport event ID.', example: 1201)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/sport-event.detail.json', 200, description: 'Example Sport Event Detail')]
    #[ResponseFromFile('app/Docs/Api/V1/Response/404.json', 404, description: 'Sport event not found')]
    public function detail(SportEvent $sportEvent): JsonResponse
    {
        $sportEvent->load([
            'sportClasses.classDefinition',
            'sportDiscipline',
            'sportLevel',
            'sportEventLinks',
            'sportServices',
            'relayTeams.members',
        ]);

        $classes = $sportEvent->sportClasses
            ->map(static fn (SportClass $sportClass): array => [
                'id' => $sportClass->id,
                'oris_id' => $sportClass->oris_id,
                'name' => $sportClass->name,
                'distance' => $sportClass->distance,
                'climbing' => $sportClass->climbing,
                'controls' => $sportClass->controls,
                'fee' => $sportClass->fee,
                'legs' => $sportClass->legs,
                'class_definition' => $sportClass->classDefinition !== null ? [
                    'id' => $sportClass->classDefinition->id,
                    'name' => $sportClass->classDefinition->name,
                    'gender' => $sportClass->classDefinition->gender,
                    'age_from' => $sportClass->classDefinition->age_from,
                    'age_to' => $sportClass->classDefinition->age_to,
                ] : null,
            ])
            ->values()
            ->all();

        $services = $sportEvent->sportServices
            ->map(static fn (SportService $service): array => [
                'id' => $service->id,
                'name' => $service->service_name_cz,
                'unit_price' => $service->unit_price,
                'qty_available' => $service->qty_available,
                'qty_remaining' => $service->qty_remaining,
                'last_booking_date_time' => $service->last_booking_date_time,
            ])
            ->values()
            ->all();

        $links = $sportEvent->sportEventLinks
            ->map(static fn (SportEventLink $link): array => [
                'id' => $link->id,
                'name' => $link->name_cz ?? $link->name_en,
                'url' => $link->source_url,
                'type' => $link->source_type->value,
            ])
            ->values()
            ->all();

        $relayTeams = $sportEvent->relayTeams
            ->map(static fn (RelayTeam $team): array => [
                'id' => $team->id,
                'name' => $team->name,
                'relay_type' => $team->relay_type,
                'slots_count' => $team->slots_count,
                'members' => $team->members
                    ->map(static fn (RelayTeamMember $member): array => [
                        'relay_team_member_id' => $member->id,
                        'slot' => $member->slot,
                        'occupied' => $member->user_entry_id !== null,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();

        $stageOptions = [];
        for ($stage = 1; $stage <= (int) $sportEvent->stages; $stage++) {
            $stageOptions[] = 'stage'.$stage;
        }

        return JsonResource::make([
            'id' => $sportEvent->id,
            'name' => $sportEvent->name,
            'alt_name' => $sportEvent->alt_name,
            'oris_id' => $sportEvent->oris_id,
            'use_oris_for_entries' => $sportEvent->use_oris_for_entries,
            'date' => $sportEvent->date?->toDateString(),
            'date_end' => $sportEvent->date_end?->toDateString(),
            'place' => $sportEvent->place,
            'organization' => $sportEvent->organization,
            'region' => $sportEvent->region,
            'entry_desc' => $sportEvent->entry_desc,
            'event_info' => $sportEvent->event_info,
            'event_warning' => $sportEvent->event_warning,
            'event_type' => [
                'value' => $sportEvent->event_type?->value,
                'label' => $sportEvent->event_type !== null ? __('sport-event.type_enum.'.$sportEvent->event_type->value) : null,
            ],
            'discipline' => $sportEvent->sportDiscipline !== null ? [
                'id' => $sportEvent->sportDiscipline->id,
                'short_name' => $sportEvent->sportDiscipline->short_name,
                'long_name' => $sportEvent->sportDiscipline->long_name,
            ] : null,
            'level' => $sportEvent->sportLevel !== null ? [
                'id' => $sportEvent->sportLevel->id,
                'short_name' => $sportEvent->sportLevel->short_name,
                'long_name' => $sportEvent->sportLevel->long_name,
            ] : null,
            'is_relay' => $sportEvent->isRelayDiscipline(),
            'cancelled' => $sportEvent->cancelled,
            'cancelled_reason' => $sportEvent->cancelled_reason,
            'ranking' => $sportEvent->ranking,
            'ranking_coefficient' => $sportEvent->ranking_coefficient,
            'entry_dates' => [
                'entry_date_1' => $sportEvent->entry_date_1?->format('Y-m-d H:i:s'),
                'entry_date_2' => $sportEvent->entry_date_2?->format('Y-m-d H:i:s'),
                'entry_date_3' => $sportEvent->entry_date_3?->format('Y-m-d H:i:s'),
                'last_entry_date' => $sportEvent->lastEntryDate()?->format('Y-m-d H:i:s'),
            ],
            'entry_deadline_passed' => AppHelper::allowModifyUserEntry($sportEvent),
            'start_time' => $sportEvent->start_time,
            'gps' => [
                'lat' => $sportEvent->gps_lat,
                'lon' => $sportEvent->gps_lon,
            ],
            'stages' => $sportEvent->stages,
            'stage_options' => $stageOptions,
            'classes' => $classes,
            'services' => $services,
            'links' => $links,
            'relay_teams' => $relayTeams,
            'created_at' => $sportEvent->created_at,
            'updated_at' => $sportEvent->updated_at,
        ])->response();
    }
}
