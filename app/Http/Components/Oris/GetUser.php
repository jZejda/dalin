<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Response\OrisUser;
use Illuminate\Http\Client\Response;

final class GetUser extends OrisResponse
{
    public function data(Response $response): OrisUser
    {
        $data = $this->getDataResponseString($response->json(self::ORIS_DEFAULT_DATA));
        return $this->getSerializer()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\OrisUser',
            'json'
        );
    }
}
