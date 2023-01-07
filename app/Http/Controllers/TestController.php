<?php

namespace App\Http\Controllers;

use App\Mail\SendSportEventNearestMail;
use App\Models\SportEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TestController extends Controller
{
    public function test(): View
    {

//        $response = Http::get('https://oris.orientacnisporty.cz/API',
//            [
//                'format' => 'json',
//                'method' => 'getEvent',
//                'id' => '7012',
//                //'id' => '2024',
//            ])
//            ->throw()
//            ->json('Data');

//        $sportEvents = SportEvent::

        $firsTimeNotify =



            // Upozorneni dva dny předem pred prvním terminem
//       $sportEventsFirst = DB::table('sport_events')
//           ->whereBetween('entry_date_1', [Carbon::now()->addDay(1), Carbon::now()->addDay(2)])
//           ->get();
//
//
//
//
//
//        dd($sportEvents);

        Mail::to('zejda.jiri@gmail.com')->send(new SendSportEventNearestMail());



        dd('jhoto');




        return view('test', [
            'test' => 'test'
        ]);
    }
}
