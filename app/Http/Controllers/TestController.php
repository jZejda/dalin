<?php

namespace App\Http\Controllers;

use App\Http\Components\Oris\Response\OrisUserEntity;
use App\Http\Components\Oris\GetUser;
use App\Mail\SendSportEventNearestMail;
use App\Models\SportEvent;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TestController extends Controller
{
    /**
     * @throws RequestException
     */
    public function test(): void
    {

        $orisResponse = Http::get('https://oris.orientacnisporty.cz/API',
            [
                'format' => 'json',
                'method' => 'getUser',
                'rgnum' => 'ABM7805',
            ])
            ->throw();


        $user = new GetUser();
        $data = null;
        if ($user->checkOrisResponse($orisResponse)) {
            $data = $user->data($orisResponse);

            // some stuff

        }

        var_dump($data->getID());
        die;


        // dd($response->json());
        // $this->notification();

    }
}
