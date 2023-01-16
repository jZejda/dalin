<?php

namespace App\Http\Controllers;

use App\Http\Components\Oris\GetEvent;
use App\Http\Components\Oris\GetUser;
use App\Models\SportEvent;
use App\Models\SportService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    /**
     * @throws RequestException
     * @noinspection PhpMultipleClassDeclarationsInspection
     */
    public function test(): void
    {

        $sportEvent = SportEvent::findOrFail(1);

        //dd($sportEvent);


        $sportEventServices = $sportEvent->sportServices;

        foreach ($sportEventServices as $sportEventService) {
            /** @var SportService  $sportEventService*/
            dd($sportEventService->service_name_cz);
        }

        //dd($sportEventServices);

//        /**
//         * @var SportEvent $pokus
//         */
//        dd($pokus = SportEvent::find(1)->get());
//
//
//        $orisResponse = Http::get('https://oris.orientacnisporty.cz/API',
//            [
//                'format' => 'json',
//                'method' => 'getEvent',
//                'id' => 2252,
//            ])
//            ->throw();
//
//        //dd($orisResponse->body());
//
//
//        $event = new GetEvent();
//        $data = null;
//        if ($event->checkOrisResponse($orisResponse)) {
//
//            //dd('sem tu');
//            $data = $event->data($orisResponse);
//
//            // some stuff
//
//        }
//
//        foreach ($data->getServices() as $service) {
//
//            dd($service->getNameCZ());
//
//        }
//
//        dd($data->getServices());
//        die;
//
//
//        // dd($response->json());
//        // $this->notification();

    }

    public function test2()
    {

    }
}
