<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Response\OrisEvent;
use Illuminate\Http\Client\Response;

final class GetEvent extends OrisResponse
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
}
