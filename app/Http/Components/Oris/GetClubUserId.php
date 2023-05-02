<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use Illuminate\Http\Client\Response;

class GetClubUserId extends OrisResponse
{
    public function data(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');

        return $this->getSerializerArray()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\Entity\ClubUser[]',
            'json'
        );
    }
}
