<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Components\Oris\GetEvent;
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

        $event = new GetEvent();
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
                $serviceModel->unit_price = $service->getUnitPrice();
                $serviceModel->qty_available = $service->getQtyAvailable();
                $serviceModel->qty_remaining = $service->getQtyRemaining();
                $serviceModel->save();
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
