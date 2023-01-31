<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Shared\BaseResponse;
use Illuminate\Http\Client\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class OrisResponse
{
    public const ORIS_DEFAULT_DATA = 'Data';

    public function checkOrisResponse(Response $response): bool
    {
        $responseData = $this->response($response);
        if ($responseData->getStatus() === 'OK') {
            return true;
        }
        return false;
    }

    public function getSerializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }

    public function getSerializerArray(): Serializer
    {
        return new Serializer(
            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );
    }

    public function getResponseArrayPart(Response $response, string $jsonPath): string
    {
        $jsonFromPath = $this->getDataResponseString($response->json($jsonPath));

        $array = [];
        foreach (json_decode($jsonFromPath) as $data) {
            $array[] = $data;
        }

        return json_encode($array);
    }

    public function getDataResponseString(array $response): string
    {
        $dataString = json_encode($response);
        if ($dataString !== false) {
            return $dataString;
        }
        return '';
    }

    public function response(Response $response): BaseResponse
    {
        return $this->getSerializer()->deserialize(
            $response,
            'App\Http\Components\Oris\Shared\BaseResponse',
            'json'
        );
    }
}
