<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SportClassDefinition;
use App\Http\Components\Oris\GetClassDefinitions;
use App\Http\Components\Oris\GetOris;
use App\Http\Components\Oris\Response\Entity\ClassDefinition;
use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Models\SportEvent;
use App\Models\SportService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class OrisApiService
{
    public const ORIS_API_URL = 'https://oris.orientacnisporty.cz/API';
    public const ORIS_API_DEFAULT_FORMAT = 'json';

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
                echo $class->getFee() . PHP_EOL;
                echo $class->getClassDefinition()->getGender() . PHP_EOL;
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
                // Create|Update Event
                /** @var SportClassDefinition $model */
                $model = SportClassDefinition::where('oris_id', $data->getId())->first();
                if (is_null($model)) {
                    $model = new SportClassDefinition();
                }

                $model->oris_id = $data->getID();
                $model->sport_id = $sportId;
                $model->age_from = $data->getAgeFrom();
                $model->age_to = $data->getAgeTo();
                $model->gender = $data->getGender();
                $model->name = $data->getName();
                if($model->save() === false) {
                    return false;
                }
            }
        }
        return true;
    }


    private function orisGetResponse(array $getParams): Response
    {
        $params = array_merge_recursive(['format' => self::ORIS_API_DEFAULT_FORMAT], $getParams);

        return Http::get(self::ORIS_API_URL, $params)->throw();
    }
}
