<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Components\Oris\GetEvent;
use App\Models\SportEvent;
use DB;
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
        $orisData = null;
        if ($event->checkOrisResponse($orisResponse)) {
            $orisData = $event->data($orisResponse);
        }

        $sportEvent = SportEvent::findOrFail($eventId);

        $sportEventServices = $sportEvent->sportServices;

        //update model metoda new/update

        //update services metoda new/update

        $sportEvent->oris_id = $orisData->getID();
    }


    private function orisGetResponse(array $getParams): Response
    {
        $params = array_merge_recursive(['format' => self::ORIS_API_DEFAULT_FORMAT], $getParams);

        return Http::get(self::ORIS_API_URL, $getParams)->throw();
    }
}
