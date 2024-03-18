<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Response\Entity\ClassDefinition;
use App\Http\Components\Oris\Response\Entity\Classes;
use App\Http\Components\Oris\Response\Entity\Clubs;
use App\Http\Components\Oris\Response\Entity\ClubUser;
use App\Http\Components\Oris\Response\Entity\EventEntries;
use App\Http\Components\Oris\Response\Entity\Links;
use App\Http\Components\Oris\Response\Entity\Services;
use App\Http\Components\Oris\Response\Entity\Locations;
use App\Http\Components\Oris\Response\Entity\StartList;
use App\Http\Components\Oris\Response\OrisEvent;
use App\Http\Components\Oris\Response\OrisUser;
use Illuminate\Http\Client\Response;

final class OrisMethod extends OrisResponse
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
        return $this->getSerializer()->deserialize($data, StartList::class . '[]', 'json');
    }

    /**
     * @return Services[]
     */
    public function services(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Services');
        return $this->getSerializer()->deserialize($data, Services::class . '[]', 'json');
    }

    /**
     * @return Classes[]
     */
    public function classes(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Classes');
        return $this->getSerializer()->deserialize($data, Classes::class . '[]', 'json');
    }

    /**
     * @return Links[]
     */
    public function links(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Links');
        return $this->getSerializer()->deserialize($data, Links::class . '[]', 'json');
    }

    /**
     * @return Links[]
     */
    public function documents(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Documents');
        return $this->getSerializer()->deserialize($data, Links::class . '[]', 'json');
    }

    /**
     * @return Locations[]
     */
    public function locations(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data.Locations');
        return $this->getSerializer()->deserialize($data, Locations::class . '[]', 'json');
    }

    /**
     * @return EventEntries[]
     */
    public function eventEntries(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');
        return $this->getSerializer()->deserialize($data, EventEntries::class . '[]', 'json');
    }

    public function users(Response $response): OrisUser
    {
        $data = $this->getDataResponseString($response->json(self::ORIS_DEFAULT_DATA));
        return $this->getSerializer()->deserialize($data, OrisUser::class, 'json');
    }

    /**
     * @return ClubUser[]
     */
    public function clubUsers(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');
        return $this->getSerializer()->deserialize($data, ClubUser::class . '[]', 'json');
    }

    /**
     * @return Clubs[]
     */
    public function clubs(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');
        return $this->getSerializer()->deserialize($data, Clubs::class . '[]', 'json');
    }

    /**
     * @return ClassDefinition[]
     */
    public function classDefinitions(Response $response): array
    {
        $data = $this->getResponseArrayPart($response, 'Data');

        return $this->getSerializer()->deserialize($data, ClassDefinition::class . '[]', 'json');
    }
}
