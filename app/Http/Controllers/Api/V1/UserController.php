<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserParamType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserEntry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromFile;

#[Group('User', 'Přihlášený uživatel — profil, závodní profily, přihlášky a zůstatek kreditu.')]
final class UserController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return JsonResource::make($request->user())->response();
    }

    /**
     * Seznam závodních profilů
     *
     * Vrátí závodní profily přihlášeného uživatele (výchozí jen aktivní).
     */
    #[QueryParam('all', 'boolean', description: 'When true, includes both active and inactive race profiles. Defaults to false (active only).', required: false, example: false)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/user.race-profiles.json', 200, description: 'Example User Race Profiles')]
    public function raceProfiles(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $query = $user->userRaceProfiles();

        $includeAll = filter_var($request->query('all', false), FILTER_VALIDATE_BOOLEAN);
        if ($includeAll === false) {
            $query->where('active', true);
        }

        $raceProfiles = $query
            ->orderByDesc('active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get([
                'id',
                'reg_number',
                'first_name',
                'last_name',
                'email',
                'phone',
                'gender',
                'active',
                'active_until',
                'created_at',
                'updated_at',
            ])
            ->map(static fn ($profile): array => [
                'id' => $profile->id,
                'reg_number' => $profile->reg_number,
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'full_name' => $profile->user_race_full_name,
                'email' => $profile->email,
                'phone' => $profile->phone,
                'gender' => $profile->gender,
                'active' => $profile->active,
                'active_until' => $profile->active_until?->toDateString(),
                'created_at' => $profile->created_at,
                'updated_at' => $profile->updated_at,
            ]);

        return JsonResource::collection($raceProfiles)->response();
    }

    /**
     * Seznam přihlášek uživatele
     *
     * Vrátí přihlášky všech závodních profilů uživatele, výchozí od dneška dál.
     */
    #[QueryParam('from', 'string', description: 'Date from in Y-m-d format. Filters by sport event date.', required: false, example: '2026-01-01')]
    #[QueryParam('to', 'string', description: 'Date to in Y-m-d format. Filters by sport event date.', required: false, example: '2026-12-31')]
    #[QueryParam('page', 'integer', description: 'Page number for pagination.', required: false, example: 1)]
    #[QueryParam('per_page', 'integer', description: 'Number of items per page. Defaults to 20.', required: false, example: 20)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/user.entry.list.json', 200, description: 'Example User Entry List')]
    public function entry(Request $request): JsonResponse
    {
        $request->validate([
            'from'     => ['nullable', 'date_format:Y-m-d'],
            'to'       => ['nullable', 'date_format:Y-m-d'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $raceProfileIds = $user->userRaceProfiles()->pluck('id');

        $query = UserEntry::query()
            ->with(['sportEvent', 'userRaceProfile'])
            ->whereIn('user_race_profile_id', $raceProfileIds)
            ->whereHas('sportEvent', static function ($sportEventQuery) use ($request): void {
                $from = $request->query('from');
                $to = $request->query('to');

                if ($from !== null) {
                    $sportEventQuery->whereDate('date', '>=', Carbon::parse((string) $from)->toDateString());
                } else {
                    $sportEventQuery->whereDate('date', '>=', Carbon::today()->toDateString());
                }

                if ($to !== null) {
                    $sportEventQuery->whereDate('date', '<=', Carbon::parse((string) $to)->toDateString());
                }
            });

        $entries = $query
            ->orderByDesc('entry_created')
            ->simplePaginate((int) $request->query('per_page', 20));

        $entries->getCollection()->transform(static fn (UserEntry $entry): array => [
            'id' => $entry->id,
            'sport_event' => [
                'id' => $entry->sportEvent?->id,
                'name' => $entry->sportEvent?->name,
                'date' => $entry->sportEvent?->date?->toDateString(),
            ],
            'race_profile' => [
                'id' => $entry->userRaceProfile?->id,
                'name' => $entry->userRaceProfile?->user_race_full_name,
            ],
            'class_name' => $entry->class_name,
            'requested_start' => $entry->requested_start,
            'rent_si' => $entry->rent_si,
            'entry_stages' => $entry->entry_stages,
            'entry_status' => $entry->entry_status->value,
            'entry_created' => $entry->entry_created?->format('Y-m-d H:i:s'),
            'created_at' => $entry->created_at,
            'updated_at' => $entry->updated_at,
        ]);

        return JsonResource::collection($entries)->response();
    }

    /**
     * Zůstatek konta uživatele
     *
     * Vrátí aktuální zůstatek kreditu přihlášeného uživatele.
     */
    #[ResponseFromFile('app/Docs/Api/V1/Response/user.credit-balance.json', 200, description: 'Example User Credit Balance')]
    public function creditBalance(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $balance = $user->getParam(UserParamType::UserActualBalance);
        if ($balance === null) {
            $balance = DB::table('user_credits')
                ->where('user_id', '=', $user->id)
                ->sum('amount');

            $user->setParam(UserParamType::UserActualBalance, $balance);
        }

        return JsonResource::make([
            'amount' => (float) $balance,
            'currency' => UserCredit::CURRENCY_CZK,
            'updated_at' => null,
        ])->response();
    }
}
