<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class TestController extends Controller
{
    public function test(): View
    {

        $response = Http::get('https://oris.orientacnisporty.cz/API',
            [
                'format' => 'json',
                'method' => 'getEvent',
                'id' => '7012',
                //'id' => '2024',
            ])
            ->throw()
            ->json('Data');


     dd($response);




        return view('test', [
            'test' => 'test'
        ]);
    }
}
