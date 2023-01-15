<?php

namespace App\Http\Controllers;

use App\Http\Components\Oris\Response\OrisUserEntity;
use App\Http\Components\Oris\User;
use App\Mail\SendSportEventNearestMail;
use App\Models\SportEvent;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TestController extends Controller
{
    public function test(): void
    {

        $response = Http::get('https://jsonplaceholder.typicode.com/posts/1');
        $pokus = new User();
        $responseObject = $pokus->getUser($response->body());


        var_dump($responseObject->getTitle());
        die;


        // dd($response->json());
        // $this->notification();

    }
}
