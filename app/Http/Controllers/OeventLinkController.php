<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OeventLinkController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('clearance')->except('index', 'show');
    }


    public function show()
    {

        return view('admin.oevents.link.show');


    }

}
