<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Components\OpenMap\OpenMapResponse;
use App\Http\Components\OpenMap\Response\BaseResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class OpenMapService
{
    private ?OpenMapResponse $openMapResponse;

    public const OPEN_MAP_API_URL = 'https://api.openweathermap.org/data/2.5/forecast';
    public const OPEN_MAP_DEFAULT_FORMAT = 'json';

    public function __construct(?OpenMapResponse $openMapResponse = null)
    {
        $this->openMapResponse = $openMapResponse ?? new OpenMapResponse();
    }

    public function getWeather(float $lat = null, float $lon = null): BaseResponse
    {
        $getParams = [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => env('OPEN_MAP_API_KEY', 'key'),
            'lang' => 'cz',
            'units' => 'metric',
        ];
        $openMapResponse = $this->openMapGetResponse($getParams);

        //$forecast = new OpenMapResponse();
        return $this->openMapResponse->response($openMapResponse);
    }

    private function openMapGetResponse(array $getParams): Response
    {
        $params = array_merge_recursive(['mode' => self::OPEN_MAP_DEFAULT_FORMAT], $getParams);
        return Http::get(self::OPEN_MAP_API_URL, $params)->throw();
    }
}
