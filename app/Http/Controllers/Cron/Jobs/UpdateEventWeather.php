<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Http\Components\OpenMap\Response\ListResponse;
use App\Models\SportEvent;
use App\Services\OpenMapService;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateEventWeather
{
    private ?OpenMapService $openMapService;

    public function __construct(?OpenMapService $openMapService = null)
    {
        $this->openMapService = $openMapService ?? new OpenMapService();
    }

    public function run(): void
    {
        $sportEvents = SportEvent::whereNotNull('gps_lat')
            ->whereNotNull('gps_lon')
            ->whereNotNull('date')
            ->whereNotNull('start_time')
            ->where('date', '>=', Carbon::now()->format(AppHelper::MYSQL_DATE_TIME))
            ->where('date', '<=', Carbon::now()->addDays(5)->format(AppHelper::MYSQL_DATE_TIME))
            ->where('cancelled', '!=', 1)
            ->get();

        /**
         * @var SportEvent $event
         */
        foreach ($sportEvents as $event) {
            if($event->gps_lon != '0.0' || $event->gps_lat != '0.0') {
                $weather = $this->openMapService->getWeather((float)$event->gps_lat, (float)$event->gps_lon);

                $dateExplode = explode(' ', (string)$event->date);
                $eventStartTimeStamp = Carbon::createFromFormat(AppHelper::MYSQL_DATE_TIME, $dateExplode[0] . ' ' . (string)$event->start_time)->timestamp;

                $findForecast = false;
                /** @var ListResponse $weatherForecast */
                foreach ($weather->getList() as $weatherForecast) {
                    if ($weatherForecast['dt'] > $eventStartTimeStamp && !$findForecast) {
                        $findForecast = true;
                        $event->update(['weather' => $weatherForecast]);
                        Log::channel('app')->info('Weather update at event' . $event->id . ' id.');
                    }
                }
            }
        }
    }
}
