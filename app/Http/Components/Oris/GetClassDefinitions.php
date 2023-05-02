<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use Illuminate\Http\Client\Response;

class GetClassDefinitions extends OrisResponse
{
    public function data(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');

        return $this->getSerializerArray()->deserialize(
            $data,
            'App\Http\Components\Oris\Response\Entity\ClassDefinition[]',
            'json'
        );
    }
}
