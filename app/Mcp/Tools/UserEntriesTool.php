<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\User;
use App\Models\UserEntry;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí přihlášky uživatele na sportovní události s filtrováním podle data.')]
#[IsReadOnly]
class UserEntriesTool extends Tool
{
    public function handle(Request $request): Response
    {
        $user = $this->resolveUser($request);

        if ($user === null) {
            return Response::error('Parametr user_id je povinný nebo použijte HTTP transport s x-apikey autentizací.');
        }

        $raceProfileIds = $user->userRaceProfiles()->pluck('id');

        $query = UserEntry::query()
            ->with(['sportEvent', 'userRaceProfile'])
            ->whereIn('user_race_profile_id', $raceProfileIds)
            ->whereHas('sportEvent', static function ($q) use ($request): void {
                $from = $request->get('from');
                $to   = $request->get('to');

                if ($from !== null) {
                    $q->whereDate('date', '>=', Carbon::parse((string) $from)->toDateString());
                } else {
                    $q->whereDate('date', '>=', Carbon::today()->toDateString());
                }

                if ($to !== null) {
                    $q->whereDate('date', '<=', Carbon::parse((string) $to)->toDateString());
                }
            });

        $perPage = min((int) ($request->get('per_page', 20) ?: 20), 100);
        $entries = $query->orderByDesc('entry_created')->simplePaginate($perPage);

        $entries->getCollection()->transform(static fn (UserEntry $entry): array => [
            'id'          => $entry->id,
            'sport_event' => [
                'id'   => $entry->sportEvent?->id,
                'name' => $entry->sportEvent?->name,
                'date' => $entry->sportEvent?->date?->toDateString(),
            ],
            'race_profile'    => [
                'id'   => $entry->userRaceProfile?->id,
                'name' => $entry->userRaceProfile?->user_race_full_name,
            ],
            'class_name'      => $entry->class_name,
            'requested_start' => $entry->requested_start,
            'rent_si'         => $entry->rent_si,
            'entry_stages'    => $entry->entry_stages,
            'entry_status'    => $entry->entry_status->value,
            'entry_created'   => $entry->entry_created?->format('Y-m-d H:i:s'),
            'created_at'      => $entry->created_at,
            'updated_at'      => $entry->updated_at,
        ]);

        return Response::json($entries->toArray());
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id'  => $schema->integer()->description('ID uživatele. Vyžadováno při stdio transportu.'),
            'from'     => $schema->string()->description('Datum od (Y-m-d). Výchozí: dnes.'),
            'to'       => $schema->string()->description('Datum do (Y-m-d).'),
            'per_page' => $schema->integer()->description('Počet záznamů na stránku (max 100, výchozí 20).'),
        ];
    }

    private function resolveUser(Request $request): ?User
    {
        $authUser = auth()->user();
        if ($authUser instanceof User) {
            return $authUser;
        }

        $userId = $request->get('user_id');
        if ($userId === null) {
            return null;
        }

        $found = User::find((int) $userId);

        return $found instanceof User ? $found : null;
    }
}
