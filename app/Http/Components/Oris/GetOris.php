<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Response\OrisEvent;
use Illuminate\Http\Client\Response;

final class GetOris extends OrisResponse
{
    public function data(Response $response): OrisEvent
    {
        $data = $this->getDataResponseString($response->json(self::ORIS_DEFAULT_DATA));
        return $this->getSerializer()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\OrisEvent',
            'json'
        );
    }

    public function services(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Services');
        return $this->getSerializerArray()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\Entity\Services[]',
            'json'
        );
    }

    public function classes(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Classes');
        return $this->getSerializerArray()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\Entity\Classes[]',
            'json'
        );
    }

    public function links(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Links');
        return $this->getSerializerArray()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\Entity\Links[]',
            'json'
        );
    }


}
