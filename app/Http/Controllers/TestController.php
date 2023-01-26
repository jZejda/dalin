<?php

namespace App\Http\Controllers;

use App\Http\Components\Oris\CreateEntry;
use App\Http\Components\Oris\GetEvent;
use App\Http\Components\Oris\GuzzleClient;
use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Http\Controllers\Cron\CronTabManager;
use App\Http\Controllers\Cron\OrisUpdateEntry;
use App\Models\SportService;
use App\Models\UserRaceProfile;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    use ManagesFrequencies;

    private GuzzleClient $client;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public function test(): void
    {

        (new OrisUpdateEntry())->update(7721);
        dd('jede');

    }

    public function testsss(): void
    {
        $params = [
            'entryid' => '2249267',
        ];

        $client = $this->client->create();
        $clientResponse = $client->request('POST', 'API', $this->client->generateMultipartForm(GuzzleClient::METHOD_DELETE_ENTRY, $params));


        //dd($clientResponse->getBody()->getContents());

        $response = new CreateEntry();
        $orisResponse = $response->data($clientResponse->getBody()->getContents());

        //dd($orisResponse->getData()->getEntry()->getID()); //teroreticky ID prihlasky
        dd($orisResponse->getStatus()); //status ORISU melo by byt OK
        dd($orisResponse->getExportCreated()); //cas prihlasky
    }

    public function entryDelete(): void
    {
        $params = [
            'entryid' => '2249267',
        ];

        $client = $this->client->create();
        $clientResponse = $client->request('POST', 'API', $this->client->generateMultipartForm(GuzzleClient::METHOD_DELETE_ENTRY, $params));


        //dd($clientResponse->getBody()->getContents());

        $response = new CreateEntry();
        $orisResponse = $response->data($clientResponse->getBody()->getContents());

        //dd($orisResponse->getData()->getEntry()->getID()); //teroreticky ID prihlasky
        dd($orisResponse->getStatus()); //status ORISU melo by byt OK
        dd($orisResponse->getExportCreated()); //cas prihlasky

    }




    public function testPok(): void
    {
        $params = [
            'clubuser' => '54',
            'class' => '167282',
        ];

        $client = $this->client->create();
        $clientResponse = $client->request('POST', 'API', $this->client->getMultipartParams($params));

//        var_dump($clientResponse->getBody()->getContents());
//        die;

        //{"Method":"createEntry","Format":"json","Status":"OK","ExportCreated":"2023-01-20 01:07:57","Data":{"Entry":{"ID":2248669}}}

        $response = new CreateEntry();
        $orisResponse = $response->data($clientResponse->getBody()->getContents());

        dd($orisResponse->getData()->getEntry()->getID()); //teroreticky ID prihlasky
        dd($orisResponse->getStatus()); //status ORISU melo by byt OK
        dd($orisResponse->getExportCreated()); //cas prihlasky


    }


    public function createEntry()
    {

        $params = [
            'clubuser' => '54',
            'class' => '167282',
        ];

        $client = $this->client->create();
        $clientResponse = $client->request('POST', 'API', $this->client->generateMultipartForm(GuzzleClient::METHOD_CREATE_ENTRY, $params));

        //dd($clientResponse->getBody()->getContents());


        $response = new CreateEntry();
        $orisResponse = $response->data($clientResponse->getBody()->getContents());

        dd($orisResponse->getStatus());
        dd($orisResponse->getData()?->getEntry()->getID()); //teroreticky ID prihlasky
        dd($orisResponse->getExportCreated()); //cas prihlasky

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
