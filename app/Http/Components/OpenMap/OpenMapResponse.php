<?php

declare(strict_types=1);

namespace App\Http\Components\OpenMap;

use App\Http\Components\OpenMap\Response\BaseResponse;
use Illuminate\Http\Client\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class OpenMapResponse
{
    public function checkOpenMapResponse(Response $response): bool
    {
        $responseData = $this->response($response);
        if ($responseData->getCod() === '200') {
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

    public function response(Response $response): BaseResponse
    {
        return $this->getSerializer()->deserialize(
            $response,
            'App\Http\Components\OpenMap\Response\BaseResponse',
            'json'
        );
    }
}
