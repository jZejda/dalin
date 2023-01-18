<?php

namespace App\Http\Controllers;

use App\Http\Components\Oris\GetEvent;
use App\Http\Components\Oris\GetUser;
use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Models\SportEvent;
use App\Models\SportService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function test(): void
    {

        $orisResponse = Http::withHeaders([
            'X-First' => 'foo',
            'X-Second' => 'bar'
        ])->post('https://oris.orientacnisporty.cz/API',
            [
                'format' => 'json',
                'method' => 'createEntry',
                'username' => 'zejda.jiri',
                'password' => '54455464',
                'clubuser' => '54',
                'comp' => '7721',
            ])->body();

        var_dump($orisResponse);


//        id kategorie 167282
//
//    id clena klubu: 54



    }

    public function test2()
    {

        $orisResponse = Http::get('https://oris.orientacnisporty.cz/API',
            [
                'format' => 'json',
                'method' => 'getEvent',
                'id' => 7721,
            ])
            ->throw();



        $event = new GetEvent();
        if ($event->checkOrisResponse($orisResponse)) {

            $classes = $event->classes($orisResponse);
            foreach ($classes as $class) {
                /** @var Classes $class */
                echo $class->getFee() . PHP_EOL;
                echo $class->getClassDefinition()->getGender() . PHP_EOL;
            }

            $services = $event->services($orisResponse);

            foreach ($services as $service) {
                /** @var Services $service */
                echo $service->getNameCZ() .PHP_EOL;
            }

        }

    }

    public function test3()
    {
        $orisResponse = Http::get('https://oris.orientacnisporty.cz/API',
            [
                'format' => 'json',
                'method' => 'getValidClasses',
                'clubuser' => '54',
                'comp' => '7721',
            ])->body();

        var_dump($orisResponse);

        die;
    }
}
