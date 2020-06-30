<?php

namespace App\Http\Controllers;

use App\Oevent;
use App\Oevent_leg;
use App\Discipline;
use Carbon\Carbon;
use Session;

use Illuminate\Http\Request;

class OeventLegController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('clearance'); //TODO dodelat prava
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $oevnt_id
     * @return \Illuminate\Http\Response
     */
    public function create($oevent_id)
    {

            $oevent = Oevent::where('id', '=', $oevent_id)->first();
            $legs_in_event = Oevent_leg::where('oevent_id', '=', $oevent_id)->get();
            $discipline = Discipline::orderBy('id')->pluck('long_name', 'id');

        return view('admin.oevents.legs.create', ['oevent' => $oevent, 'legs_in_event' => $legs_in_event, 'discipline' => $discipline]);
    }


    /*
     * Store a newly created Leg in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'         => 'required',
            'leg_datetime'  => 'required|date_format:d.m.Y H:i:s',
            'oris_id'       => 'nullable|integer',
            'lat'           => 'required',
            'lon'           => 'required',
        ]);

        $input = $request->all();
        null !== $request['leg_datetime'] ? $input['leg_datetime'] = Carbon::createFromFormat('d.m.Y H:i:s', $request['leg_datetime'])->toDateTimeString() : null ;

        //dd($input);

        Oevent_leg::create($input);
        Session::flash('flash_message', 'Založil jsi novou etapu: '.$request->title);

        return redirect()->route('oevents.index');
    }


}
