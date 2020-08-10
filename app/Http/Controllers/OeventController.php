<?php

namespace App\Http\Controllers;

use App\Oevent_leg;
use Session;
use Storage;
use App\Oevent;
use App\User;
use App\Discipline;
use App\Region;
use App\Sport;
use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class OeventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('clearance')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $disciplines = Discipline::get();
        $disciplines = Arr::pluck($disciplines, 'long_name', 'id');

        $sports = Sport::get();
        $sports = Arr::pluck($sports, 'name', 'id');

        $regions = Region::get();
        $regions = Arr::pluck($regions, 'short_name', 'id');


        $event_category = array(
            1 => 'Závod', 2 => 'Trénink', 3 => 'Soustředění', 4 => 'Tábor'
            );

        //$oevents = Oevent::with('legs')->get();
        $oevents = Oevent::with(array('legs' => function($query) {
            $query->orderBy('leg_datetime', 'ASC');
        }))->where('from_date', '>=', Carbon::today())
            ->orderBy('from_date', 'asc')
            ->limit(3)
            ->get();

        //dump($oevents);

        return view('admin.oevents.index', ['oevents' => $oevents, 'disciplines' => $disciplines, 'regions' => $regions, 'sports' => $sports, 'event_category' => $event_category]);
    }

    /*
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$sport = Sport::get();

        $oris_export_clubs = Storage::disk('orisdata')->get('exports/all_clubs_latest.json');
        $oris_export_clubs = json_decode($oris_export_clubs, true);

        $clubs = array();
        foreach ($oris_export_clubs['Data'] as $club) {
            $clubs[$club['Abbr']] = $club['Abbr'] .' - '. $club['Name'];
        }

        if(config('site-config.oevent-config.custom_organizer')!== null) {
            $custom_clubs = config('site-config.oevent-config.custom_organizer');

            $clubs = array_merge($clubs, $custom_clubs);
        }

        //dd($clubs);

        //$clubs = Sport::orderBy('id')->pluck('name', 'id');
        $sport = Sport::orderBy('id')->pluck('name', 'id');
        $regions = Region::orderBy('id')->pluck('short_name', 'id');


        //dump($sports);

        return view('admin.oevents.create', ['clubs' => $clubs, 'sport' => $sport, 'regions' => $regions]);
    }

    /*
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'         => 'required',
            'place'         => 'required',
            'first_date'    => 'required|date_format:d.m.Y H:i:s',
            'second_date'   => 'nullable|date_format:d.m.Y H:i:s',
            'third_date'    => 'nullable|date_format:d.m.Y H:i:s',
            'from_date'     => 'required|date_format:d.m.Y',
            'to_date'       => 'nullable|date_format:d.m.Y',
            'clubs'         => 'required',
            'regions'       => 'required',
            'sport_id'      => 'required',
            'url'           => 'nullable|url',
            'oris_id'       => 'nullable|integer',
            'event_category'=> 'required',
        ], [
            'title.required' => 'Doplň název',
            'place.required'  => 'Doplň místo',
            'first_date.required'  => 'Vyplň první termín',
            'clubs.required' => 'Vyber klub/y',
            'regions.required' => 'Vyber region/y'
        ]);

        $input = $request->all();

        //null !== $request['date_from'] ? $input['date_from'] = Carbon::createFromFormat('d.m.Y', $request['date_from'])->toDateString() : null ;
        //null !== $request['date_to'] ? $input['date_to'] = Carbon::createFromFormat('d.m.Y', $request['date_to'])->toDateString() : null ;

        null !== $request['first_date'] ? $input['first_date'] = Carbon::createFromFormat('d.m.Y H:i:s', $request['first_date'])->toDateTimeString() : null ;
        null !== $request['second_date'] ? $input['second_date'] = Carbon::createFromFormat('d.m.Y H:i:s', $request['second_date'])->toDateTimeString() : null ;
        null !== $request['third_date'] ? $input['third_date'] = Carbon::createFromFormat('d.m.Y H:i:s', $request['third_date'])->toDateTimeString() : null ;

        null !== $request['from_date'] ? $input['from_date'] = Carbon::createFromFormat('d.m.Y', $request['from_date'])->toDateString()  : null ;
        null !== $request['to_date'] ? $input['to_date'] = Carbon::createFromFormat('d.m.Y', $request['to_date'])->toDateString() : null ;

        null !== $request['clubs'] ? $input['clubs'] = json_encode($input['clubs'])  : null ;
        null !== $request['regions'] ? $input['regions'] = json_encode($input['regions'])  : null ;


        //dd($input['first_date']);
        //dd($input);
    /*
        if ($request['private'] == 'on') {
            $input['private'] = 1;
        }

        if ($request['img_url'] == null) {
            $input['img_url'] = DEFFAULT_IMG_URL;
        }
    */

        Oevent::create($input);
        Session::flash('flash_message', 'Založil jsi novou akci '.$request->title);

        $last_index_event = Oevent::select('id')->latest('created_at')->first();

        return redirect()->route('legs.create.first', $last_index_event);
        //return url('admin/legs/create/').$last_index_event;
    }

    /*
     * Display the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Oevent $oevent)
    {
        $legs = Oevent_leg::where('oevent_id', '=', $oevent->id)->get();
        dump($legs);
        return view('admin.oevents.show', ['oevent' => $oevent, 'legs' => $legs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        //dump($page);
        $category = Content_category::orderBy('id')->pluck('title', 'id');
        $users_editor = User::orderBy('name')->pluck('name', 'id');

        return view('admin.pages.edit', compact('page', 'category', 'users_editor'));
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'slug' => 'required|unique:pages,slug,'.$page->id,
        ]);

        $input = $request->all();
        $page->fill($input)->save();

        if (! array_key_exists('page_menu', $input)) {
            Page::where('id', $page->id)->update(['page_menu' => 0]);
        }

        //Session::flash('flash_message', 'Upravil jsi stranku.');

        return redirect()
            ->route('pages.show', $page->id)
            ->with('flash_message', 'Právě jsi aktualizoval stránku: '.$page->title);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Oevent $oevent)
    {
        $oevent->delete();

        return redirect()->route('oevents.index')
            ->with('flash_message', 'Údálost byla smazána.');
    }


    public function createLeg()
    {
        return 'jede';
    }


    public function event_leg_info($eventid, $legid)
    {
        $oevent = Oevent::where('id', '=', $eventid)->first();

        $legs = Oevent_leg::where('oevent_id', '=', $eventid)->get();

        return view('admin.oevents.show', ['oevent' => $oevent, 'legs' => $legs]);
    }

    // DEVEL classes

    /**
     * Display a listing of the resource.
     * @param null $year
     * @param null $from (now, allyear)
     */
    public function get_oevent_data_api($year, $from)
    {

        if ($from == 'now') {
            $from_date = date("Y-m-d");
        } elseif ( $from == 'year') {
            $from_date = date("Y").'-01-01';
        } else {
            $from_date = '';
        }


        $sports = Sport::get();
        $sports = Arr::pluck($sports, 'name', 'id');

        $regions = Region::get();
        $regions = Arr::pluck($regions, 'short_name', 'id');


        $event_category = array(
            1 => 'Závod', 2 => 'Trénink', 3 => 'Soustředění', 4 => 'Tábor'
        );

        //$oevents = Oevent::with('legs')->get();
        $oevents = Oevent::with(array('legs' => function($query) {
            $query->orderBy('leg_datetime', 'ASC');
        })) ->whereYear('created_at', $year)
            ->where('from_date', '>', $from_date)
            ->orderBy('from_date', 'asc')
            ->get();


        return response()->json([
            'oevents' => $oevents,
            'regions' => $regions,
            'sports'   => $sports,
            'category' => $event_category
        ], 200);

    }


}
