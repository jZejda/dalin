<?php

namespace App\Http\Components\Oris;

use Illuminate\Http\Client\Response;

class GetClubs extends OrisResponse
{
    public function data(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');

        return $this->getSerializerArray()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\Entity\Clubs[]',
            'json'
        );
    }
}
