<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SportEventLinkType;
use App\Enums\SportEventType;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Http\Components\Oris\OrisMethod;
use App\Http\Components\Oris\Response\Entity\ClassDefinition;
use App\Http\Components\Oris\Response\Entity\EventEntries;
use App\Http\Components\Oris\Response\Entity\Links;
use App\Http\Components\Oris\Response\Entity\Locations;
use App\Http\Components\Oris\Response\Entity\News;
use App\Models\Club;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\SportEventLink;
use App\Models\SportEventMarker;
use App\Models\SportEventNews;
use App\Models\SportRegion;
use App\Models\SportService;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Models\UserRaceProfile;
use App\Shared\Helpers\AppHelper;
use App\Shared\Helpers\EmptyType;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class OrisApiService
{
    private OrisResponse $orisResponse;

    private OrisMethod $orisMethod;

    public const string ORIS_API_URL = 'https://oris.orientacnisporty.cz/API';

    public const string ORIS_API_DEFAULT_FORMAT = 'json';

    public function __construct(?OrisResponse $orisResponse = null, ?OrisMethod $orisMethod = null)
    {
        $this->orisResponse = $orisResponse ?? new OrisResponse();
        $this->orisMethod = $orisMethod ?? new OrisMethod();
    }

    //getEventStartLists
    public function updateStartList(int $eventId): bool
    {
        $getParams = [
            'method' => 'getEvent',
            'id' => $eventId,
        ];

        $orisResponse = $this->orisGetResponse($getParams);

        $startList = new OrisMethod();

        if ($startList->checkOrisResponse($orisResponse)) {
            $orisData = $startList->data($orisResponse);
        }

        return true;

    }

    /**
     * @throws Throwable
     */
    public function updateEvent(int $eventId, bool $updateByCron = false): bool
    {
        $getParams = [
            'method' => 'getEvent',
            'id' => $eventId,
        ];
        $orisResponse = $this->orisGetResponse($getParams);

        $event = new OrisMethod();
        if ($event->checkOrisResponse($orisResponse)) {
            $orisData = $event->data($orisResponse);

            // Create|Update Event
            /** @var SportEvent $eventModel */
            $eventModel = SportEvent::query()
                ->where('oris_id', '=', $eventId)
                ->first();

            $newEvent = false;
            if (is_null($eventModel)) {
                $eventModel = new SportEvent();
                $newEvent = true;
            }

            $regions = [];
            if (str_contains(', ', $orisData->Region)) {
                $orisRegions = explode(', ', $orisData->Region);
                foreach ($orisRegions as $region) {
                    $regions[] = $region;
                }
            } else {
                $regions[] = $orisData->Region;
            }

            $eventModel->name = $orisData->Name;
            $eventModel->oris_id = $eventId;
            $eventModel->date = $orisData->Date;
            $eventModel->place = $orisData->Place;
            $eventModel->sport_id = (int) $orisData->Sport->ID;
            $eventModel->discipline_id = (int) $orisData->Discipline->ID;
            $eventModel->level_id = (int) $orisData->Level->ID;

            $eventModel->start_time = $orisData->StartTime;

            $eventModel->entry_date_1 = strlen($orisData->EntryDate1) !== 0 ? $orisData->EntryDate1 : null;

            //dd(!$eventModel->dont_update_excluded);
            if ($newEvent || ! $eventModel->dont_update_excluded) {
                $eventModel->entry_date_2 = strlen($orisData->EntryDate2) !== 0 ? $orisData->EntryDate2 : null;
                $eventModel->entry_date_3 = strlen($orisData->EntryDate3) !== 0 ? $orisData->EntryDate3 : null;
                $eventModel->use_oris_for_entries = true;
            }

            $eventModel->increase_entry_fee_2 = strlen($orisData->EntryKoef2 ?? '') !== 0 ? $orisData->EntryKoef2 : null;
            $eventModel->increase_entry_fee_3 = strlen($orisData->EntryKoef3 ?? '') !== 0 ? $orisData->EntryKoef3 : null;

            $organization = [$orisData->Org1->Abbr];
            if (! is_null($orisData->Org2?->Abbr)) {
                $organization[] = $orisData->Org2->Abbr;
            }
            $eventModel->organization = $organization;
            $eventModel->region = $regions;
            $eventModel->entry_desc = $orisData->EntryInfo;
            $eventModel->event_info = $orisData->EventInfo;
            $eventModel->event_warning = $orisData->EventWarning;
            $eventModel->ranking = $orisData->Ranking;
            $eventModel->gps_lat = $orisData->GPSLat;
            $eventModel->gps_lon = $orisData->GPSLon;
            $eventModel->event_type = SportEventType::Race->value;
            $eventModel->stages = EmptyType::stringNotEmpty($orisData->Stages) ? (int) $orisData->Stages : 0;
            $eventModel->multi_events = EmptyType::stringNotEmpty($orisData->MultiEvents) ? (int) $orisData->MultiEvents : 0;
            $eventModel->stages = (! is_null($orisData->Stages) || (int) $orisData->Stages != 0) ? (int) $orisData->Stages : null;
            $eventModel->parent_id = (! is_null($orisData->ParentID) || (int) $orisData->ParentID != 0) ? (int) $orisData->ParentID : null;
            if ($updateByCron) {
                $eventModel->last_update = Carbon::now();
            }

            $eventModel->saveOrFail();

            // Create|Update Classes
            $classes = $event->classes($orisResponse);
            foreach ($classes as $class) {
                $classModel = SportClass::query()
                    ->where('sport_event_id', '=', $eventModel->id)
                    ->where('oris_id', '=', $class->ID)
                    ->first();

                if (is_null($classModel)) {
                    $classModel = new SportClass();
                }

                /** @var SportClassDefinition $classDefinitionModel */
                $classDefinitionModel = SportClassDefinition::query()
                    ->where('oris_id', '=', $class->ClassDefinition->ID)
                    ->first();

                if ($classDefinitionModel === null) {
                    $classDefinitionModel = new SportClassDefinition();
                    $classDefinitionModel = $this->saveClassDefinitionModel($classDefinitionModel, $class->ClassDefinition, (int) $orisData->Sport->ID);
                }

                $classModel->sport_event_id = $eventModel->id;
                $classModel->oris_id = (int) $class->ID;
                $classModel->class_definition_id = $classDefinitionModel->id;
                $classModel->name = $class->Name;
                $classModel->distance = $class->Distance;
                $classModel->controls = $class->Controls;
                $classModel->fee = (float) $class->Fee;
                $classModel->saveOrFail();
            }

            // Create|Update Services
            $services = $event->services($orisResponse);
            foreach ($services as $service) {
                /** @var SportService $serviceModel */
                $serviceModel = SportService::query()
                    ->where('sport_event_id', '=', $eventModel->id)
                    ->where('oris_service_id', '=', $service->ID)
                    ->first();

                if (is_null($serviceModel)) {
                    $serviceModel = new SportService();
                }

                $serviceModel->sport_event_id = $eventModel->id;
                $serviceModel->oris_service_id = (int) $service->ID;
                $serviceModel->service_name_cz = $service->NameCZ;
                $serviceModel->last_booking_date_time = $service->LastBookingDateTime;
                $serviceModel->unit_price = floatval($service->UnitPrice);
                $serviceModel->qty_available = intval($service->QtyAvailable);
                $serviceModel->qty_remaining = intval($service->QtyRemaining);
                $serviceModel->saveOrFail();
            }

            // Create|Update Links
            $documents = $event->documents($orisResponse);
            $links = $event->links($orisResponse);

            $activeLinksDocumentsIds = [];
            foreach ($documents as $document) {
                $activeLinksDocumentsIds[] = $document->ID;
            }
            foreach ($links as $link) {
                $activeLinksDocumentsIds[] = $link->ID;
            }
            $this->updateLinksDocuments($documents, $eventModel);
            $this->updateLinksDocuments($links, $eventModel);
            $this->clearOldLinksDocuments($activeLinksDocumentsIds, $eventModel);

            // Create|Update locations alias markers
            $markers = $event->locations($orisResponse);
            $this->updateMarkers($markers, $eventModel);

            /**
             * @description Create|Update SportEventNews
             */
            $news = $event->news($orisResponse);
            $this->updateNews($news, $eventModel);

        }

        return true;
    }

    public function updateClassDefinitions(int $sportId): bool
    {
        $getParams = [
            'method' => 'getClassDefinitions',
            'sport' => $sportId,
        ];
        $orisResponse = $this->orisGetResponse($getParams);

        if ($this->orisMethod->checkOrisResponse($orisResponse)) {

            $orisData = $this->orisMethod->classDefinitions($orisResponse);

            foreach ($orisData as $data) {
                // Create|Update SportClassDefinition
                /** @var SportClassDefinition $model */
                $model = SportClassDefinition::query()
                    ->where('oris_id', $data->ID)
                    ->first();

                if (is_null($model)) {
                    $model = new SportClassDefinition();
                }

                $this->saveClassDefinitionModel($model, $data, $sportId);
            }
        }

        return true;
    }

    public function updateClubs(): SystemSyncDetail
    {
        $getParams = [
            'method' => 'getCSOSClubList',
        ];
        $orisResponse = $this->orisGetResponse($getParams);

        if ($this->orisMethod->checkOrisResponse($orisResponse)) {

            $orisData = $this->orisMethod->clubs($orisResponse);

            foreach ($orisData as $data) {
                // Create|Update Club
                /** @var Club $model */
                $model = Club::query()
                    ->where('abbr', $data->Abbr)
                    ->first();

                if (is_null($model)) {
                    $model = new Club();
                    $this->orisResponse->newItem($data->Abbr.' | '.$data->Name);
                } else {
                    $this->orisResponse->updatedItem($data->Abbr.' | '.$data->Name);
                }
                $model->abbr = $data->Abbr;
                $model->name = $data->Name;
                $regionId = SportRegion::query()->where('long_name', '=', $data->Region)->first();
                $model->region_id = ! is_null($regionId) ? $regionId->id : 1;
                $model->oris_id = $data->ID;
                $model->oris_number = $data->Number;
                $model->saveOrFail();
            }

            $this->orisResponse->setStatus($this->orisMethod->response($orisResponse)->getStatus());

            return $this->orisResponse->getItemsInfo();
        }

        $this->orisResponse->setStatus($this->orisMethod->response($orisResponse)->getStatus());

        return $this->orisResponse->getItemsInfo();
    }

    private function saveClassDefinitionModel(SportClassDefinition $model, ClassDefinition $data, int $sportId): SportClassDefinition
    {
        $model->oris_id = (int) $data->ID;
        $model->sport_id = $sportId;
        $model->age_from = (int) $data->AgeFrom;
        $model->age_to = (int) $data->AgeTo;
        $model->gender = $data->Gender;
        $model->name = $data->Name;

        try {
            if ($model->save() === false) {
                throw new ApiStoreResponseException(
                    message: 'Can\'t save SportClassDefinition model OrisId '.$data->ID,
                    model: 'SportClassDefinition',
                    userId: Auth::id(),
                );
            }
        } catch (ApiStoreResponseException $error) {
            Log::channel('site')->error($error->getStoreError());
        }

        return $model;
    }

    public function getEventBalance(SportEvent $sportEvent, string $source = UserCredit::SOURCE_USER): void
    {
        $getParams = [
            'method' => 'getEventEntries',
            'clubid' => 1, // TODO dej do params
            'eventid' => $sportEvent->oris_id,
        ];
        $orisResponse = $this->orisGetResponse($getParams);

        if ($this->orisMethod->checkOrisResponse($orisResponse)) {

            /** @var EventEntries[] $orisData */
            $orisData = $this->orisMethod->eventEntries($orisResponse);

            foreach ($orisData as $entry) {
                $regUserNumber = $entry->RegNo;

                /** @var UserRaceProfile $userRaceProfile */
                $userRaceProfile = DB::table('user_race_profiles')
                    ->where('reg_number', '=', $regUserNumber)
                    ->first();

                // TODO cekni jestli uz to neni prirazeno
                if ($userRaceProfile !== null) {

                    /** @var UserCredit $userCredit */
                    $userCredit = UserCredit::query()
                        ->where('oris_balance_id', '=', $entry->ID)
                        ->first();

                    if ($userCredit === null) {
                        $userCredit = new UserCredit();
                        $userCredit->status = UserCreditStatus::Done;
                    }

                    $userCredit->oris_balance_id = $entry->ID;
                    $userCredit->user_id = $userRaceProfile->user_id;
                    $userCredit->user_race_profile_id = $userRaceProfile->id;
                    $userCredit->sport_event_id = $sportEvent->id;
                    $userCredit->amount = -(float) $entry->Fee;
                    //$userCredit->balance = $this->getBalance($userRaceProfile->user, -(float)$entry->getFee());
                    //$userCredit->balance = -(float)$entry->getFee();
                    $userCredit->currency = UserCredit::CURRENCY_CZK;
                    $userCredit->credit_type = UserCreditType::CacheOut;
                    $userCredit->source = $source;
                    $userCredit->source_user_id = auth()->user()->id;
                    $userCredit->created_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);
                    $userCredit->updated_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);

                    $userCredit->saveOrFail();
                } else {
                    /** @var UserCredit $userCredit */
                    $userCredit = UserCredit::query()
                        ->where('oris_balance_id', '=', $entry->ID)
                        ->first();

                    if ($userCredit === null) {
                        $userCredit = new UserCredit();
                        $userCredit->status = UserCreditStatus::UnAssign;
                    }
                    $userCredit->oris_balance_id = $entry->ID;
                    $userCredit->user_id = null;
                    $userCredit->user_race_profile_id = null;
                    $userCredit->sport_event_id = $sportEvent->id;
                    $userCredit->amount = -(float) $entry->Fee;
                    $userCredit->currency = UserCredit::CURRENCY_CZK;
                    $userCredit->credit_type = UserCreditType::CacheOut;
                    $userCredit->source = UserCredit::SOURCE_USER;
                    $userCredit->source_user_id = auth()->user()->id;
                    $userCredit->created_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);
                    $userCredit->updated_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);

                    $userCredit->saveOrFail();
                }

                $userCreditNote = UserCreditNote::query()
                    ->where('internal', '=', true)
                    ->where('user_credit_id', '=', $userCredit->id)
                    ->count();
                if ($userCreditNote === 0) {
                    $this->createUserCreditSystemNote($userCredit, $entry);
                }
            }

            $sportEvent->last_calculate_cost = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);
            $sportEvent->saveOrFail();
        }
    }

    private function createUserCreditSystemNote(UserCredit $userCredit, EventEntries $entry): void
    {
        $entryParams = [
            'id' => $entry->ID,
            'classDesc' => $entry->ClassDesc,
            'regNo' => $entry->RegNo,
            'name' => $entry->Name,
            'firstName' => $entry->FirstName,
            'lastName' => $entry->LastName,
            'rentSI' => $entry->RentSI,
            'userID' => $entry->UserID,
            'clubUserID' => $entry->ClubUserID,
            'fee' => -(float) $entry->Fee,
            'note' => $entry->Note,
            'entryStop' => $entry->EntryStop,
            'CreatedDateTime' => $entry->CreatedDateTime,
            'CreatedByUserID' => $entry->CreatedByUserID,
            'UpdatedDateTime' => $entry->UpdatedDateTime,
            'UpdatedByUserID' => $entry->UpdatedByUserID,
        ];

        $model = new UserCreditNote();
        $model->note_user_id = 1;
        $model->user_credit_id = $userCredit->id;
        $model->note = 'Data načtena z ORISu. Informace k záznamu byly v pořádku uloženy do databáze.';
        $model->internal = true;
        $model->params = $entryParams;
        $model->saveOrFail();
    }

    private function orisGetResponse(array $getParams): Response
    {
        $params = array_merge_recursive(['format' => self::ORIS_API_DEFAULT_FORMAT], $getParams);

        return Http::get(self::ORIS_API_URL, $params)->throw();
    }

    private function getBalance(User $user, float $amouth): float
    {

        /** @var UserCredit $lastCase */
        $lastCase = DB::table('user_credits')
            ->where('user_id', '=', $user->id)
            ->whereNotNull('balance')
            ->orderBy('modified_at', 'desc')
            ->first();

        if (! is_null($lastCase)) {
            return $amouth;
        }

        return $lastCase?->balance;

    }

    /**
     * @throws Throwable
     */
    public function updateUserClubId(): bool
    {

        $userRaceProfiles = DB::table('user_race_profiles')
            ->whereNotNull('oris_id')
            ->get();

        foreach ($userRaceProfiles as $userRaceProfile) {
            $getParams = [
                'method' => 'getClubUsers',
                'user' => $userRaceProfile->oris_id,
            ];

            $orisResponse = $this->orisGetResponse($getParams);

            if ($this->orisMethod->checkOrisResponse($orisResponse)) {
                $orisData = $this->orisMethod->clubUsers($orisResponse);

                foreach ($orisData as $data) {
                    /** @var UserRaceProfile $model */
                    $model = UserRaceProfile::query()
                        ->where('oris_id', '=', $userRaceProfile->oris_id)
                        ->first();

                    if (! is_null($model) && $data->RegNo === $model->reg_number) {
                        $model->club_user_id = $data->ID;
                        $model->saveOrFail();
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param  Links[]  $entities
     *
     * @throws Throwable
     */
    private function updateLinksDocuments(array $entities, SportEvent $eventModel): void
    {
        foreach ($entities as $entity) {
            /** @var SportEventLink $sportEventLink */
            $sportEventLink = SportEventLink::query()
                ->where('sport_event_id', '=', $eventModel->id)
                ->where('external_key', '=', (int) $entity->ID)
                ->first();
            if (is_null($sportEventLink)) {
                $sportEventLink = new SportEventLink();
                $sportEventLink->sport_event_id = $eventModel->id;
            }

            $sportEventLink->external_key = (int) $entity->ID;
            $sportEventLink->internal = false;
            $sportEventLink->source_url = $entity->Url;
            $sportEventLink->source_type = SportEventLinkType::mapOrisIdtoEnum((int) $entity->SourceType?->ID);
            $sportEventLink->name_cz = $entity->SourceType?->NameCZ;
            $sportEventLink->name_en = $entity->SourceType?->NameEN;
            $sportEventLink->description_cz = $entity->OtherDescCZ;
            $sportEventLink->description_en = $entity->OtherDescEN;
            $sportEventLink->saveOrFail();
        }
    }

    private function clearOldLinksDocuments(array $actualLinksDocumentsIds, SportEvent $eventModel): void
    {
        $oldLinks = SportEventLink::query()
            ->where('sport_event_id', '=', $eventModel->id)
            ->whereNotNull('external_key')
            ->get();

        /** @var array<int> $oldOrisLinkIds */
        $oldOrisLinkIds = $oldLinks->pluck('external_key')->toArray();

        // ExternalKeys array to be deleted for specific SportEvent
        /** @var array<int> $deleteLinks */
        $deleteLinks = [];
        foreach ($oldOrisLinkIds as $externalKey) {
            if (! in_array($externalKey, $actualLinksDocumentsIds)) {
                $deleteLinks[] = $externalKey;
            }
        }

        foreach ($deleteLinks as $link) {
            SportEventLink::query()
                ->where('sport_event_id', '=', $eventModel->id)
                ->where('external_key', '=', $link)
                ->delete();
        }
    }

    /**
     * @param  Locations[]  $markers
     *
     * @throws Throwable
     */
    private function updateMarkers(array $markers, SportEvent $eventModel): void
    {
        foreach ($markers as $marker) {
            /** @var SportEventMarker $sportEventMarker */
            $sportEventMarker = SportEventMarker::query()
                ->where('sport_event_id', '=', $eventModel->id)
                ->where('external_key', '=', (int) $marker->ID)
                ->first();

            if (is_null($sportEventMarker)) {
                $sportEventMarker = new SportEventMarker();
                $sportEventMarker->sport_event_id = $eventModel->id;
            }
            $sportEventMarker->external_key = (int) $marker->ID;
            $sportEventMarker->letter = $marker->Letter;
            $sportEventMarker->label = $marker->NameCZ;
            $sportEventMarker->lat = (float) $marker->GPSLat;
            $sportEventMarker->lon = (float) $marker->GPSLon;
            $sportEventMarker->saveOrFail();
        }
    }

    /**
     * @param  News[]  $news
     *
     * @throws Throwable
     */
    private function updateNews(array $news, SportEvent $eventModel): void
    {
        foreach ($news as $newItem) {
            $newItemDate = Carbon::createFromFormat(AppHelper::DB_DATE_TIME, $newItem->Date);

            $sportEventNewItem = SportEventNews::query()
                ->where('sport_event_id', '=', $eventModel->id)
                ->where('external_key', '=', (int) $newItem->ID)
                ->first();

            if (is_null($sportEventNewItem)) {
                $sportEventNewItem = new SportEventNews();
                $sportEventNewItem->sport_event_id = $eventModel->id;
            }
            $sportEventNewItem->external_key = (int) $newItem->ID;
            $sportEventNewItem->text = $newItem->Text;
            $sportEventNewItem->date = $newItemDate !== false ? $newItemDate : Carbon::now();

            $sportEventNewItem->saveOrFail();
        }
    }
}
