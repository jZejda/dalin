<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí závodní profily uživatele (reg_number, jméno, pohlaví, aktivní stav, kontakt).')]
#[IsReadOnly]
class RaceProfilesTool extends Tool
{
    public function handle(Request $request): Response
    {
        $user = $this->resolveUser($request);

        if ($user === null) {
            return Response::error('Parametr user_id je povinný nebo použijte HTTP transport s x-apikey autentizací.');
        }

        $includeAll = (bool) $request->get('include_inactive', false);

        $query = $user->userRaceProfiles();

        if (! $includeAll) {
            $query->where('active', true);
        }

        $profiles = $query
            ->orderByDesc('active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'reg_number', 'first_name', 'last_name', 'email', 'phone', 'gender', 'active', 'active_until', 'created_at', 'updated_at'])
            ->map(static fn ($profile): array => [
                'id'           => $profile->id,
                'reg_number'   => $profile->reg_number,
                'first_name'   => $profile->first_name,
                'last_name'    => $profile->last_name,
                'full_name'    => $profile->user_race_full_name,
                'email'        => $profile->email,
                'phone'        => $profile->phone,
                'gender'       => $profile->gender,
                'active'       => $profile->active,
                'active_until' => $profile->active_until?->toDateString(),
                'created_at'   => $profile->created_at,
                'updated_at'   => $profile->updated_at,
            ]);

        return Response::json(['data' => $profiles->values()->all()]);
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id'          => $schema->integer()->description('ID uživatele. Vyžadováno při stdio transportu.'),
            'include_inactive' => $schema->boolean()->description('Zahrnout i neaktivní profily (výchozí: false).'),
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
