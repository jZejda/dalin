<?php

namespace App\Http\Controllers;

use Storage;
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


        $exists = Storage::disk('eventdata')->exists('/races/vysledky_iofv3.xml');

        if($exists)
        {
            $file_raw_content = Storage::disk('eventdata')->get('/races/vysledky_iofv3.xml');

            $xml = simplexml_load_string($file_raw_content, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);

            $sorted_data = array();
            $sorted_data['resultInfo'] = array(
                'iofVersion' => $array['@attributes']['iofVersion'],
                'createTime' => $array['@attributes']['createTime'],
                'creator' => $array['@attributes']['creator'],
            );
            $sorted_data['event'] = array(
                'name' => $array['Event']['Name']
            );


            foreach ($array['ClassResult'] as $class) {

                $class_short_name = $class['Class']['ShortName'];
                $class_course_data = array (
                    'courseName'        => $class['Course']['Name'],
                    'courseLenght'      => $class['Course']['Length'],
                    'courseClimb'       => $class['Course']['Climb'],
                    'courseNumControls' => $class['Course']['NumberOfControls'],
                );


                $sorted_data['resultData'][$class_short_name] = array(
                    'classCourseData' => $class_course_data,
                    );

                $personResult = $class['PersonResult'];

                foreach ($personResult as $person)
                {
                    $person_entry_id = $person['EntryId'];


                    // Person ID may have an array
                    if (isset($person['Person']['Id'])) {
                        if (is_array($person['Person']['Id'])){
                            $personi_id = $person['Person']['Id'][0];
                        } else {
                            $personi_id = $person['Person']['Id'];
                        }
                    }
                    else
                    {
                        $personi_id = '';
                    }
                    //(isset($person['Person']['Id'][0]) ? $person['Person']['Id'][0] : '')

                    $sorted_data['resultData'][$class_short_name]['personCourseData'][$person_entry_id] = array(
                        'personId' => $personi_id,
                        'familyName' => (isset($person['Person']['Name']['Family']) ? $person['Person']['Name']['Family'] : ''),
                        'givenName' => (isset($person['Person']['Name']['Given']) ? $person['Person']['Name']['Given'] : ''),
                        'personOrgLongname' => (isset($person['Organisation']['Name']) ? $person['Organisation']['Name'] : ''),
                        'personOrgShortName' => (isset($person['Organisation']['ShortName']) ? $person['Organisation']['ShortName'] : ''),
                        'personResultPosition' => (isset($person['Result']['Position']) ? $person['Result']['Position'] : ''),
                        'personResultTime' => (isset($person['Result']['Time']) ? $person['Result']['Time'] : ''),
                        'personResultStatus' => (isset($person['Result']['Status']) ? $person['Result']['Status'] : ''),
                        'personResultTimeBehind' => (isset($person['Result']['TimeBehind']) ? $person['Result']['TimeBehind'] : ''),
                        'personControlCard' => (isset($person['Result']['ControlCard']) ? $person['Result']['ControlCard'] : ''),
                    );
                }

            }


        }
        else
        {
            echo 'soubor neexistuje';
        }

        dd($sorted_data);

        return view('admin.oevents.link.show');


    }

    public function test()
    {
        return view('admin.oevents.link.event-iofv3-result');
    }

}
