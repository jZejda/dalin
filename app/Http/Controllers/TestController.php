<?php

namespace App\Http\Controllers;

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

        // $this->notification();

    }
}
