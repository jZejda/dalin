<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Links;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Http\Components\Oris\Response\Entity\Locations;
use App\Http\Components\Oris\Response\Entity\StartList;
use App\Http\Components\Oris\Response\OrisEvent;
use Illuminate\Http\Client\Response;

final class GetOris extends OrisResponse
{
    /**
     * @param Response $response
     * @return OrisEvent
     */
    public function data(Response $response): OrisEvent
    {
        $data = $this->getDataResponseString($response->json(self::ORIS_DEFAULT_DATA));
        return $this->getSerializer()->deserialize($data, OrisEvent::class, 'json');
    }

    /**
     * @return StartList[]
     */
    public function startList(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');
        return $this->getSerializerArray()->deserialize($data, StartList::class . '[]', 'json');
    }

    /**
     * @return Services[]
     */
    public function services(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Services');
        return $this->getSerializerArray()->deserialize($data, Services::class . '[]', 'json');
    }

    /**
     * @return Classes[]
     */
    public function classes(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Classes');
        return $this->getSerializerArray()->deserialize($data, Classes::class . '[]', 'json');
    }

    /**
     * @return Links[]
     */
    public function links(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Links');
        return $this->getSerializerArray()->deserialize($data, Links::class . '[]', 'json');
    }

    /**
     * @return Links[]
     */
    public function documents(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Documents');
        return $this->getSerializerArray()->deserialize($data, Links::class . '[]', 'json');
    }

    /**
     * @return Locations[]
     */
    public function locations(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Locations');
        return $this->getSerializerArray()->deserialize($data, Locations::class . '[]', 'json');
    }


}
