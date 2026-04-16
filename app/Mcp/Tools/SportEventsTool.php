<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Enums\SportEventType;
use App\Models\SportClass;
use App\Models\SportEvent;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí seznam sportovních událostí (závody, tréninky) s filtrováním podle data, typu a kategorie.')]
#[IsReadOnly]
class SportEventsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $query = SportEvent::query()->with(['sportClasses.classDefinition']);

        $from  = $request->get('from');
        $to    = $request->get('to');
        $eventType         = $request->get('event_type');
        $classDefinitionId = $request->get('class_definition_id');

        if ($from !== null) {
            $query->where('date', '>=', Carbon::parse((string) $from)->startOfDay());
        }

        if ($to !== null) {
            $query->where('date', '<=', Carbon::parse((string) $to)->endOfDay());
        }

        if (is_string($eventType) && SportEventType::tryFrom($eventType) !== null) {
            $query->where('event_type', $eventType);
        }

        if ($classDefinitionId !== null) {
            $query->whereHas('sportClasses', static function ($q) use ($classDefinitionId): void {
                $q->where('class_definition_id', (int) $classDefinitionId);
            });
        }

        $perPage = min((int) ($request->get('per_page', 20) ?: 20), 100);

        $events = $query
            ->orderByDesc('date')
            ->select(['id', 'name', 'date', 'entry_date_1', 'event_type', 'created_at', 'updated_at'])
            ->simplePaginate($perPage);

        $events->getCollection()->transform(static function (SportEvent $event): array {
            $categories = $event->sportClasses
                ->map(static fn (SportClass $sc): array => [
                    'id'   => $sc->class_definition_id,
                    'name' => $sc->classDefinition?->name,
                ])
                ->unique('id')
                ->values()
                ->all();

            return [
                'id'         => $event->id,
                'name'       => $event->name,
                'date'       => $event->date?->toDateString(),
                'entry_date' => $event->entry_date_1?->format('Y-m-d H:i:s'),
                'event_type' => [
                    'value' => $event->event_type?->value,
                    'label' => $event->event_type?->value,
                ],
                'categories' => $categories,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
            ];
        });

        return Response::json($events->toArray());
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'from'               => $schema->string()->description('Datum od (Y-m-d).'),
            'to'                 => $schema->string()->description('Datum do (Y-m-d).'),
            'event_type'         => $schema->string()->description('Typ události: race, training, trainingCamp, other.'),
            'class_definition_id' => $schema->integer()->description('Filtrovat podle ID kategorie/třídy.'),
            'per_page'           => $schema->integer()->description('Počet záznamů na stránku (max 100, výchozí 20).'),
        ];
    }
}
