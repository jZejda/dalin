<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\EntryStatus;
use App\Enums\UserParamType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreUserEntryRequest;
use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Entries\EntryCreator;
use App\Services\SportEvents\Entries\EntryDeleter;
use App\Shared\Helpers\AppHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('User', 'Přihlášený uživatel — profil, závodní profily, přihlášky a zůstatek kreditu.')]
final class UserEntryController extends Controller
{
    /**
     * Vytvoření přihlášky na závod
     *
     * Přihlásí závodní profil uživatele na závod — včetně štafet, etap a ORIS závodů.
     */
    #[BodyParam('sport_event_id', 'integer', description: 'ID of the sport event (see GET /api/v1/sport-event).', example: 1201)]
    #[BodyParam('race_profile_id', 'integer', description: 'ID of one of the authenticated user\'s race profiles (see GET /api/v1/user/race-profiles).', example: 12)]
    #[BodyParam('class_id', 'integer', description: 'ID of the event class (see classes[].id in GET /api/v1/sport-event/{id}). Required for non-relay events.', required: false, example: 351)]
    #[BodyParam('relay_team_member_id', 'integer', description: 'Free relay slot ID (see relay_teams[].members[].relay_team_member_id in the event detail). Required for relay events instead of class_id.', required: false, example: null)]
    #[BodyParam('si', 'integer', description: 'SI chip number. Defaults to the SI stored on the race profile.', required: false, example: 8123456)]
    #[BodyParam('rent_si', 'boolean', description: 'Request an SI chip rental.', required: false, example: false)]
    #[BodyParam('note', 'string', description: 'Note for the event organizer.', required: false, example: 'Note for organizer')]
    #[BodyParam('club_note', 'string', description: 'Internal club note.', required: false, example: null)]
    #[BodyParam('requested_start', 'string', description: 'Requested start time.', required: false, example: null)]
    #[BodyParam('entry_stages', 'string[]', description: 'Stages to enter for multi-stage events, e.g. ["stage1", "stage2"] (see stage_options in the event detail).', required: false)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/user.entry.store.json', 201, description: 'Entry created')]
    #[Response('{"message": "Entry deadline has passed."}', 422, description: 'Business rule rejected the entry')]
    #[Response('{"message": "Race profile already has an entry for this event."}', 409, description: 'Duplicate entry')]
    #[Response('{"message": "Race profile does not belong to the authenticated user."}', 403, description: 'Foreign race profile')]
    public function store(StoreUserEntryRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        /** @var SportEvent $sportEvent */
        $sportEvent = SportEvent::query()->findOrFail((int) $validated['sport_event_id']);

        /** @var UserRaceProfile $raceProfile */
        $raceProfile = UserRaceProfile::query()->findOrFail((int) $validated['race_profile_id']);

        if ($raceProfile->user_id !== $user->id) {
            return response()->json(['message' => 'Race profile does not belong to the authenticated user.'], 403);
        }

        if (! $raceProfile->active) {
            return response()->json(['message' => 'Race profile is not active.'], 422);
        }

        if ($sportEvent->cancelled) {
            return response()->json(['message' => 'Sport event is cancelled.'], 422);
        }

        if (AppHelper::allowModifyUserEntry($sportEvent) && ! AppHelper::allowModifyUserEntryAfterDeadline($sportEvent)) {
            return response()->json(['message' => 'Entry deadline has passed.'], 422);
        }

        if (! $this->canCreateEntry($user)) {
            return response()->json(['message' => 'Credit balance is below the club limit, entries are blocked.'], 422);
        }

        if (! $sportEvent->isRelayDiscipline() && $this->hasActiveEntry($sportEvent, $raceProfile)) {
            return response()->json(['message' => 'Race profile already has an entry for this event.'], 409);
        }

        if ($sportEvent->stages !== null && $sportEvent->stages > 0 && empty($validated['entry_stages'])) {
            return response()->json(['message' => 'Event has stages, entry_stages is required.'], 422);
        }

        $data = [
            'note' => $validated['note'] ?? null,
            'club_note' => $validated['club_note'] ?? null,
            'requested_start' => $validated['requested_start'] ?? null,
            'si' => $validated['si'] ?? $raceProfile->si,
            'rent_si' => (int) ($validated['rent_si'] ?? 0),
        ];

        if (isset($validated['entry_stages'])) {
            $data['entry_stages'] = $validated['entry_stages'];
        }

        if ($sportEvent->isRelayDiscipline()) {
            if (! isset($validated['relay_team_member_id'])) {
                return response()->json(['message' => 'relay_team_member_id is required for relay events.'], 422);
            }

            $data['raceProfileId'] = $raceProfile->id;
            $data['relayTeamMemberId'] = (int) $validated['relay_team_member_id'];
        } else {
            if (! isset($validated['class_id'])) {
                return response()->json(['message' => 'class_id is required for non-relay events.'], 422);
            }

            $sportClass = SportClass::query()
                ->where('id', '=', (int) $validated['class_id'])
                ->where('sport_event_id', '=', $sportEvent->id)
                ->first();

            if ($sportClass === null) {
                return response()->json(['message' => 'Class does not belong to the given sport event.'], 422);
            }

            if ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
                if ($raceProfile->oris_id === null) {
                    return response()->json(['message' => 'Race profile has no ORIS ID, cannot enter an ORIS event.'], 422);
                }

                if ($sportClass->oris_id === null) {
                    return response()->json(['message' => 'Class has no ORIS ID, cannot enter an ORIS event.'], 422);
                }

                // ORIS strategy looks profiles and classes up by their ORIS IDs
                $data['raceProfileId'] = (int) $raceProfile->oris_id;
                $data['classId'] = $sportClass->oris_id;
            } else {
                $data['raceProfileId'] = $raceProfile->id;
                $data['classId'] = $sportClass->id;
            }
        }

        $result = EntryCreator::make()->create($sportEvent, $data);

        if (! $result->success) {
            if ($result->orisStatusError !== null) {
                return response()->json([
                    'message' => 'ORIS rejected the entry.',
                    'oris_status' => $result->orisStatusError,
                ], 422);
            }

            return response()->json(['message' => 'Entry could not be created.'], 422);
        }

        $entry = $result->entry?->load(['sportEvent', 'userRaceProfile']);

        if ($entry === null) {
            // Relay entries do not return the created entry from the strategy
            return response()->json(['data' => ['created' => true]], 201);
        }

        return JsonResource::make($this->transformEntry($entry))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Zrušení přihlášky
     *
     * Zruší přihlášku uživatele, u ORIS závodů včetně odhlášení v ORISu.
     */
    #[UrlParam('userEntry', 'integer', description: 'User entry ID (see GET /api/v1/user/entry).', example: 5301)]
    #[ResponseFromFile('app/Docs/Api/V1/Response/user.entry.delete.json', 200, description: 'Entry cancelled')]
    #[Response('{"message": "Entry deadline has passed."}', 422, description: 'Deadline passed, entry cannot be cancelled')]
    #[Response('{"message": "Entry is already cancelled."}', 422, description: 'Already cancelled')]
    public function destroy(Request $request, UserEntry $userEntry): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($userEntry->userRaceProfile?->user_id !== $user->id) {
            // Do not reveal foreign entries
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($userEntry->entry_status === EntryStatus::Cancel) {
            return response()->json(['message' => 'Entry is already cancelled.'], 422);
        }

        $sportEvent = $userEntry->sportEvent;

        if ($sportEvent !== null
            && AppHelper::allowModifyUserEntry($sportEvent)
            && ! AppHelper::allowModifyUserEntryAfterDeadline($sportEvent)) {
            return response()->json(['message' => 'Entry deadline has passed.'], 422);
        }

        $result = EntryDeleter::make()->delete($userEntry);

        if (! $result->success) {
            return response()->json([
                'message' => 'ORIS rejected the entry cancellation.',
                'oris_status' => $result->orisStatusError,
            ], 422);
        }

        return JsonResource::make([
            'id' => $userEntry->id,
            'entry_status' => EntryStatus::Cancel->value,
            'was_oris_entry' => $result->wasOrisEntry,
        ])->response();
    }

    private function hasActiveEntry(SportEvent $sportEvent, UserRaceProfile $raceProfile): bool
    {
        return UserEntry::query()
            ->where('sport_event_id', '=', $sportEvent->id)
            ->where('user_race_profile_id', '=', $raceProfile->id)
            ->where('entry_status', '!=', EntryStatus::Cancel)
            ->exists();
    }

    /**
     * Same rule as the Filament UI, but computes the cached balance
     * first so fresh API users are not blocked by a missing param.
     */
    private function canCreateEntry(User $user): bool
    {
        if ($user->getParam(UserParamType::UserActualBalance) === null) {
            $user->setParam(UserParamType::UserActualBalance, (float) $user->userCredits()->sum('amount'));
        }

        return $user->canCreateEntry();
    }

    /** @return array<string, mixed> */
    private function transformEntry(UserEntry $entry): array
    {
        return [
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
            'oris_entry_id' => $entry->oris_entry_id,
            'entry_created' => $entry->entry_created?->format('Y-m-d H:i:s'),
            'created_at' => $entry->created_at,
            'updated_at' => $entry->updated_at,
        ];
    }
}
