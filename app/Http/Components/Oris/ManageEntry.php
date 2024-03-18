<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Response\CreateEntry;
use App\Http\Components\Oris\Response\CreateEntry as CreateEntryResponse;

class ManageEntry extends OrisResponse
{
    public function data(string $postResponse): CreateEntryResponse
    {
        return $this->getSerializer()->deserialize($postResponse, CreateEntry::class, 'json');
    }
}
