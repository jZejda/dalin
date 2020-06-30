<?php


namespace App\Http\Controllers\Cron;

use Config;
use Carbon\Carbon;
use App\Oevent_leg;

use App\Http\Traits\CronRunnerTrait;
use App\Http\Controllers\Controller;

class LegWeatherController extends Controller
{

    use CronRunnerTrait;

    const FORECAST_DAY = 12;

    public function __construct()
    {
        $this->middleware('auth');
        $this->api_path = Config::get('app-config.forecast_api.url').'/'.Config::get('app-config.forecast_api.token').'/';
    }

    public function legForecast () {

        $in_days = date('Y-m-d', time() - 3 * 60 * 60 * self::FORECAST_DAY);
        $legs = Oevent_leg::where('leg_datetime', '>', $in_days )->get();

        // M H day(1-31) Mes. DenVtydnu(0-6 0=nedele)
        // pocasi -2h
        $job_01 = '* 22 * * *';
        $job_02 = '* 09 * * *';

        if($this->checkRun($job_01) || $this->checkRun($job_02)) {

            if(count($legs) >= 1) {
                foreach ($legs as $leg){

                    $legDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $leg->leg_datetime);
                    $legDateTime = $legDateTime->format('Y-m-d\TH:i:s+00:00');

                    $uri =  $leg->lat.','.
                        $leg->lon.','.
                        $legDateTime.
                        '?lang=cs&units=si&exclude=daily,flags,minutely,hourly';

                    $endpoint = $this->api_path . $uri;

                    dump($endpoint);

                    $client = new \GuzzleHttp\Client();

                    $response = $client->request('GET', $endpoint);

                    $statusCode = $response->getStatusCode();
                    if ($statusCode == 200) {
                        $forecast_content = $response->getBody();
                        $forecast_content = json_decode($response->getBody(), true);

                        //dump ($forecast_content);

                        $update_data = array();

                        $update_data = array (
                          'time' => $forecast_content['currently']['time'],
                          'summary' => $forecast_content['currently']['summary'],
                          'icon' => $forecast_content['currently']['icon'],
                          'temperature' => $forecast_content['currently']['temperature'],
                        );

                        $leg_update = Oevent_leg::find($leg->id);
                        $leg_update->forecast = $update_data;

                        $leg_update->save();
                    }
                }
            }
        }

        return 'jede';

    }

}
