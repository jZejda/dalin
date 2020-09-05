<?php

namespace App\Http\Controllers\Tool;

use App\Oevent;
use App\Oevent_results;
use Storage;
use App\Http\Controllers\Controller;

class EventResultController extends Controller
{

    public function eventIofv3Result($id)
    {

        $result = Oevent_results::where('id', '=', $id)->first();
        $exists = Storage::disk('eventdata')->exists($result['result_path']);



        if($exists)
        {
            //$file_raw_content = Storage::disk('eventdata')->get('/races/vysledky_iofv3.xml');
            $file_raw_content = Storage::disk('eventdata')->get($result['result_path']);

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

                $class_short_name = $class['Class']['Name'];
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

                $person_order = 1; // start person order count
                foreach ($personResult as $person)
                {
                    //$person_entry_id = $person['EntryId'];
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

                    $sorted_data['resultData'][$class_short_name]['personCourseData'][$person_order] = array(
                        'personId' => $personi_id,
                        'familyName' => (isset($person['Person']['Name']['Family']) ? $person['Person']['Name']['Family'] : ''),
                        'givenName' => (isset($person['Person']['Name']['Given']) ? $person['Person']['Name']['Given'] : ''),
                        'personOrgLongname' => (isset($person['Organisation']['Name']) ? $person['Organisation']['Name'] : '-'),
                        'personOrgShortName' => (isset($person['Organisation']['ShortName']) ? $person['Organisation']['ShortName'] : '-'),
                        'personResultPosition' => (isset($person['Result']['Position']) ? $person['Result']['Position'] : ''),
                        'personResultTime' => (isset($person['Result']['Time']) ? $person['Result']['Time'] : ''),
                        'personResultStatus' => (isset($person['Result']['Status']) ? $person['Result']['Status'] : ''),
                        'personResultTimeBehind' => (isset($person['Result']['TimeBehind']) ? $person['Result']['TimeBehind'] : ''),
                        'personControlCard' => (isset($person['Result']['ControlCard']) ? $person['Result']['ControlCard'] : ''),
                    );
                    $person_order ++;
                }
            }
        }
        else
        {
            echo 'soubor neexistuje';
        }

        return response()->json([
            'sorted_data' => $sorted_data,
        ]);
    }
}
