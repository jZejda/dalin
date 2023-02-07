<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Components\Oris\GetClubs;
use App\Http\Components\Oris\Response\Entity\Clubs;
use App\Models\Club;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Http\Components\Oris\GetClassDefinitions;
use App\Http\Components\Oris\GetOris;
use App\Http\Components\Oris\Response\Entity\ClassDefinition;
use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Models\SportEvent;
use App\Models\SportRegion;
use App\Models\SportService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class OrisApiService
{
    private ?OrisResponse $orisResponse;

    public const ORIS_API_URL = 'https://oris.orientacnisporty.cz/API';
    public const ORIS_API_DEFAULT_FORMAT = 'json';

    public function __construct(?OrisResponse $orisResponse = null)
    {
        $this->orisResponse = $orisResponse ?? new OrisResponse();
    }

    public function updateEvent(int $eventId): bool
    {
        $getParams = [
            'method' => 'getEvent',
            'id' => $eventId,
        ];
        $orisResponse = $this->orisGetResponse($getParams);

        $event = new GetOris();
        if ($event->checkOrisResponse($orisResponse)) {
            $orisData = $event->data($orisResponse);

            // Create|Update Event
            /** @var SportEvent $eventModel */
            $eventModel = SportEvent::where('oris_id', $eventId)->first();
            if (is_null($eventModel)) {
                $eventModel = new SportEvent();
            }
            $eventModel->name = $orisData->getName();
            $eventModel->save();

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

                //dd($class->getClassDefinition()->getID());


                $classModel->sport_event_id = $eventModel->id;
                $classModel->oris_id = $class->getID();
                $classModel->class_definition_id = $classDefinitionModel->id;
                $classModel->distance = $class->getDistance();
                $classModel->controls = $class->getControls();
                $classModel->fee = $class->getFee();
                $classModel->save();
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
                $serviceModel->save();
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
                $model->save();
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
        $model->save();

        return $model;
    }


    private function orisGetResponse(array $getParams): Response
    {
        $params = array_merge_recursive(['format' => self::ORIS_API_DEFAULT_FORMAT], $getParams);

        return Http::get(self::ORIS_API_URL, $params)->throw();
    }
}
