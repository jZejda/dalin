<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Response\CreateEntry as CreateEntryResponse;

class CreateEntry extends OrisResponse
{
    public function data(string $postResponse): CreateEntryResponse
    {
        return $this->getSerializer()->deserialize(
            $postResponse,
            'App\Http\Components\Oris\Response\CreateEntry',
            'json'
        );
    }
}
