<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SportEventLinkType;
use App\Enums\SportEventType;
use App\Enums\UserCreditStatus;
use App\Http\Components\Oris\GetClubs;
use App\Http\Components\Oris\GetEventEntries;
use App\Http\Components\Oris\Response\Entity\Clubs;
use App\Http\Components\Oris\Response\Entity\EventEntries;
use App\Models\Club;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEventLink;
use App\Models\UserRaceProfile;
use App\Http\Components\Oris\GetClassDefinitions;
use App\Http\Components\Oris\GetClubUserId;
use App\Http\Components\Oris\GetOris;
use App\Http\Components\Oris\Response\Entity\ClassDefinition;
use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Links;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Http\Components\Oris\Response\Entity\ClubUser;
use App\Models\SportEvent;
use App\Models\SportRegion;
use App\Models\SportService;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class OrisApiService
{
    private ?OrisResponse $orisResponse;

    public const ORIS_API_URL = 'https://oris.orientacnisporty.cz/API';
    public const ORIS_API_DEFAULT_FORMAT = 'json';

    public function __construct(?OrisResponse $orisResponse = null)
    {
        $this->orisResponse = $orisResponse ?? new OrisResponse();
    }

    public function updateEvent(int $eventId, bool $updateByCron = false): bool
    {
        $getParams = [
            'method' => 'getEvent',
            'id' => $eventId,
        ];
        $orisResponse = $this->orisGetResponse($getParams);

        $event = new GetOris();
        if ($event->checkOrisResponse($orisResponse)) {
            $orisData = $event->data($orisResponse);

            //dd(strlen($orisData->getEntryDate1()) !== 0 ? $orisData->getEntryDate1() : null);

            // Create|Update Event
            /** @var SportEvent $eventModel */
            $eventModel = SportEvent::where('oris_id', $eventId)->first();
            $newEvent = false;
            if (is_null($eventModel)) {
                $eventModel = new SportEvent();
                $newEvent = true;
            }

            $regions = [];
            if (str_contains(', ', $orisData->getRegion())) {
                $orisRegions = explode(', ', $orisData->getRegion());
                foreach($orisRegions as $region) {
                    $regions[] = $region;
                }
            } else {
                $regions[] = $orisData->getRegion();
            }

            $eventModel->name = $orisData->getName();
            $eventModel->oris_id = $eventId;
            $eventModel->date = $orisData->getDate();
            $eventModel->place = $orisData->getPlace();
            $eventModel->sport_id = (int)$orisData->getSport()->getID();
            $eventModel->discipline_id = (int)$orisData->getDiscipline()->getID();
            $eventModel->level_id = (int)$orisData->getLevel()->getID();

            $eventModel->start_time = $orisData->getStartTime();

            $eventModel->entry_date_1 = strlen($orisData->getEntryDate1()) !== 0 ? $orisData->getEntryDate1() : null;
            if ($newEvent || !$eventModel->use_oris_for_entries) {
                $eventModel->entry_date_2 = strlen($orisData->getEntryDate2()) !== 0 ? $orisData->getEntryDate2() : null;
                $eventModel->entry_date_3 = strlen($orisData->getEntryDate3()) !== 0 ? $orisData->getEntryDate3() : null;
            }

            $organization = [$orisData->getOrg1()->getAbbr()];
            if (!is_null($orisData->getOrg2()?->getAbbr())) {
                $organization[] = $orisData->getOrg2()?->getAbbr();
            }
            $eventModel->organization = $organization;
            $eventModel->region = $regions;
            $eventModel->entry_desc = $orisData->getEntryInfo();
            $eventModel->event_info = $orisData->getEventInfo();
            $eventModel->event_warning = $orisData->getEventWarning();
            $eventModel->ranking = $orisData->getRanking();
            $eventModel->gps_lat = $orisData->getGPSLat();
            $eventModel->gps_lon = $orisData->getGPSLon();
            $eventModel->use_oris_for_entries = true;
            $eventModel->event_type = SportEventType::Race->value;
            $eventModel->stages = (!is_null($orisData->getStages()) || (int)$orisData->getStages() != 0) ? (int)$orisData->getStages() : null;
            $eventModel->parent_id = (!is_null($orisData->getParentID()) || (int)$orisData->getParentID() != 0) ? (int)$orisData->getParentID() : null;
            if ($updateByCron) {
                $eventModel->last_update = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);
            }

            $eventModel->saveOrFail();

            // Create|Update Classes
            $classes = $event->classes($orisResponse);
            foreach ($classes as $class) {
                /** @var Classes $class */
                $classModel = SportClass::where(['sport_event_id' => $eventModel->id, 'oris_id' => $class->getID()])->first();
                if (is_null($classModel)) {
                    $classModel = new SportClass();
                }

                /** @var SportClassDefinition $classDefinitionExist */
                $classDefinitionModel = SportClassDefinition::where('oris_id', '=', $class->getClassDefinition()->getID())->first();
                if ($classDefinitionModel === null) {
                    $classDefinitionModel = new SportClassDefinition();
                    $classDefinitionModel = $this->saveClassDefinitionModel($classDefinitionModel, $class->getClassDefinition(), $eventModel->id);
                }

                $classModel->sport_event_id = $eventModel->id;
                $classModel->oris_id = $class->getID();
                $classModel->class_definition_id = $classDefinitionModel->id;
                $classModel->name = $class->getName();
                $classModel->distance = $class->getDistance();
                $classModel->controls = $class->getControls();
                $classModel->fee = $class->getFee();
                $classModel->saveOrFail();
            }

            // Create|Update Services
            $services = $event->services($orisResponse);
            /** @var Services $service */
            foreach ($services as $service) {
                /** @var SportService $serviceModel */
                $serviceModel = SportService::where(['sport_event_id' => $eventModel->id, 'oris_service_id' => $service->getID()])->first();
                if (is_null($serviceModel)) {
                    $serviceModel = new SportService();
                }

                $serviceModel->sport_event_id = $eventModel->id;
                $serviceModel->oris_service_id = $service->getID();
                $serviceModel->service_name_cz = $service->getNameCZ();
                $serviceModel->last_booking_date_time = $service->getLastBookingDateTime();
                $serviceModel->unit_price = floatval($service->getUnitPrice());
                $serviceModel->qty_available = intval($service->getQtyAvailable());
                $serviceModel->qty_remaining = intval($service->getQtyRemaining());
                $serviceModel->saveOrFail();
            }

            // Create|Update Links
            /** @var Links[] $links */
            $links = $event->links($orisResponse);
            foreach ($links as $link) {
                /** @var SportEventLink $sportEventLink */
                $sportEventLink = SportEventLink::where(['sport_event_id' => $eventModel->id, 'external_key' => (int)$link->getID()])->first();
                if (is_null($sportEventLink)) {
                    $sportEventLink = new SportEventLink();
                    $sportEventLink->sport_event_id = $eventModel->id;
                }

                $sportEventLink->external_key = (int)$link->getID();
                $sportEventLink->internal = false;
                $sportEventLink->source_url = $link->getUrl();
                $sportEventLink->source_type = SportEventLinkType::mapOrisIdtoEnum((int)$link->getSourceType()?->getID()); // tady namapovat ID na enum
                $sportEventLink->name_cz = $link->getSourceType()?->getNameCZ();
                $sportEventLink->name_en = $link->getSourceType()?->getNameEN();
                $sportEventLink->description_cz = $link->getOtherDescCZ();
                $sportEventLink->description_en = $link->getOtherDescEN();
                $sportEventLink->saveOrFail();
            }
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

        $classDefinitions = new GetClassDefinitions();
        if ($classDefinitions->checkOrisResponse($orisResponse)) {

            /** @var ClassDefinition[] $orisData */
            $orisData = $classDefinitions->data($orisResponse);

            foreach ($orisData as $data) {
                // Create|Update SportClassDefinition
                /** @var SportClassDefinition $model */
                $model = SportClassDefinition::where('oris_id', $data->getId())->first();
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

        $clubs = new GetClubs();
        if ($clubs->checkOrisResponse($orisResponse)) {

            /** @var Clubs[] $orisData */
            $orisData = $clubs->data($orisResponse);

            foreach ($orisData as $data) {
                // Create|Update Club
                /** @var Club $model */
                $model = Club::where('abbr', $data->getAbbr())->first();
                if (is_null($model)) {
                    $model = new Club();
                    $this->orisResponse->newItem($data->getAbbr() . ' | ' . $data->getName());
                } else {
                    $this->orisResponse->updatedItem($data->getAbbr() . ' | ' . $data->getName());
                }
                $model->abbr = $data->getAbbr();
                $model->name = $data->getName();
                $regionId = SportRegion::where('long_name', '=', $data->getRegion())->first();
                $model->region_id = !is_null($regionId) ? $regionId->id : 1;
                $model->oris_id = $data->getID();
                $model->oris_number = $data->getNumber();
                $model->saveOrFail();
            }

            $this->orisResponse->setStatus($clubs->response($orisResponse)->getStatus());
            return $this->orisResponse->getItemsInfo();
        }

        $this->orisResponse->setStatus($clubs->response($orisResponse)->getStatus());
        return $this->orisResponse->getItemsInfo();
    }

    private function saveClassDefinitionModel(SportClassDefinition $model, ClassDefinition $data, int $sportId): SportClassDefinition
    {
        $model->oris_id = $data->getID();
        $model->sport_id = $sportId;
        $model->age_from = $data->getAgeFrom();
        $model->age_to = $data->getAgeTo();
        $model->gender = $data->getGender();
        $model->name = $data->getName();

        try {
            if ($model->save() === false) {
                throw new ApiStoreResponseException(
                    message: 'Can\'t save SportClassDefinition model OrisId ' . $data->getID(),
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

        $eventEntries = new GetEventEntries();
        if ($eventEntries->checkOrisResponse($orisResponse)) {

            /** @var EventEntries[] $orisData */
            $orisData = $eventEntries->data($orisResponse);

            foreach ($orisData as $entry) {
                $regUserNumber = $entry->getRegNo();

                /** @var UserRaceProfile $userRaceProfile */
                $userRaceProfile = DB::table('user_race_profiles')
                    ->where('reg_number', '=', $regUserNumber)
                    ->first();

                // TODO cekni jestli uz to neni prirazeno
                if ($userRaceProfile !== null) {

                    /** @var UserCredit $userCredit */
                    $userCredit = UserCredit::where('oris_balance_id', '=', $entry->getID())->first();
                    if ($userCredit === null) {
                        $userCredit = new UserCredit();
                        $userCredit->status = UserCreditStatus::Done;
                    }

                    $userCredit->oris_balance_id = $entry->getID();
                    $userCredit->user_id = $userRaceProfile->user_id;
                    $userCredit->user_race_profile_id = $userRaceProfile->id;
                    $userCredit->sport_event_id = $sportEvent->id;
                    $userCredit->amount = -(float)$entry->getFee();
                    //$userCredit->balance = $this->getBalance($userRaceProfile->user, -(float)$entry->getFee());
                    //$userCredit->balance = -(float)$entry->getFee();
                    $userCredit->currency = UserCredit::CURRENCY_CZK;
                    $userCredit->credit_type = UserCredit::CREDIT_TYPE_CACHE_OUT;
                    $userCredit->source = $source;
                    $userCredit->source_user_id = auth()->user()->id;
                    $userCredit->created_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);
                    $userCredit->updated_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);

                    $userCredit->saveOrFail();
                } else {
                    /** @var UserCredit $userCredit */
                    $userCredit = UserCredit::where('oris_balance_id', '=', $entry->getID())->first();
                    if ($userCredit === null) {
                        $userCredit = new UserCredit();
                        $userCredit->status = UserCreditStatus::UnAssign;
                    }
                    $userCredit->oris_balance_id = $entry->getID();
                    $userCredit->user_id = null;
                    $userCredit->user_race_profile_id = null;
                    $userCredit->sport_event_id = $sportEvent->id;
                    $userCredit->amount = -(float)$entry->getFee();
                    $userCredit->currency = UserCredit::CURRENCY_CZK;
                    $userCredit->credit_type = UserCredit::CREDIT_TYPE_CACHE_OUT;
                    $userCredit->source = UserCredit::SOURCE_USER;
                    $userCredit->source_user_id = auth()->user()->id;
                    $userCredit->created_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);
                    $userCredit->updated_at = Carbon::now()->format(AppHelper::MYSQL_DATE_TIME);

                    $userCredit->saveOrFail();
                }


                $userCreditNote = UserCreditNote::where('internal', '=', true)
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
            'id' => $entry->getID(),
            'classDesc' => $entry->getClassDesc(),
            'regNo' => $entry->getRegNo(),
            'name' => $entry->getName(),
            'firstName' => $entry->getFirstName(),
            'lastName' => $entry->getLastName(),
            'rentSI' => $entry->getRentSI(),
            'userID' => $entry->getUserID(),
            'clubUserID' => $entry->getClubUserID(),
            'fee' => -(float)$entry->getFee(),
            'note' => $entry->getNote(),
            'entryStop' => $entry->getEntryStop(),
            'CreatedDateTime' => $entry->getCreatedDateTime(),
            'CreatedByUserID' => $entry->getCreatedByUserID(),
            'UpdatedDateTime' => $entry->getUpdatedDateTime(),
            'UpdatedByUserID' => $entry->getUpdatedByUserID(),
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

        /** @var UserCredit $lastCase*/
        $lastCase = DB::table('user_credits')
            ->where('user_id', '=', $user->id)
            ->whereNotNull('balance')
            ->orderBy('modified_at', 'desc')
            ->first();

        if (!is_null($lastCase)) {
            return $amouth;
        }

        return $lastCase?->balance;

    }

    public function updateUserClubId(): bool
    {

        $userRaceProfiles= DB::table('user_race_profiles')
                ->whereNotNull('oris_id')
                ->get();

        foreach ($userRaceProfiles as $userRaceProfile) {
            $getParams = [
                'method' => 'getClubUsers',
                'user' => $userRaceProfile->oris_id,
            ];
            $orisResponse = $this->orisGetResponse($getParams);

            $clubUserId = new GetClubUserId();
            if ($clubUserId->checkOrisResponse($orisResponse)) {

                /** @var ClubUser[] $orisData */
                $orisData = $clubUserId->data($orisResponse);

                foreach ($orisData as $data) {
                    /** @var UserRaceProfile $model */
                    $model = UserRaceProfile::where('oris_id', $userRaceProfile->oris_id)->first();
                    if (!is_null($model) && $data->getRegNo() === $model->reg_number) {
                        $model->club_user_id = $data->getID();
                        $model->saveOrFail();
                    }
                }
            }
        }
        return true;
    }

}
