<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use App\Http\Components\Oris\Shared\BaseResponse;
use App\Shared\Helpers\AppHelper;
use Illuminate\Http\Client\Response;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
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
        $jsonEncoder = new JsonEncoder();
        $csvEncoder = new CsvEncoder();
        $reflectionExtractor = new ReflectionExtractor();
        $docExtractor = new PhpDocExtractor();
        $propertyExtractor = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$docExtractor, $reflectionExtractor],
            [$docExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor],
        );

        $attributeLoader = new AttributeLoader();
        $classMetaDataFactory = new ClassMetadataFactory($attributeLoader);
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetaDataFactory);
        $objectNormalizer = new ObjectNormalizer($classMetaDataFactory, $metadataAwareNameConverter, null, $propertyExtractor);

        $jsonSerializableNormalizer = new JsonSerializableNormalizer();
        $arrayNormalizer = new ArrayDenormalizer();
        $backedEnumNormalizer = new BackedEnumNormalizer();
        $context = [
            DateTimeNormalizer::FORMAT_KEY => AppHelper::MYSQL_DATE_TIME,
        ];
        $dateTimeNormalizer = new DateTimeNormalizer($context);

        return new Serializer([
            $backedEnumNormalizer,
            $jsonSerializableNormalizer,
            $objectNormalizer,
            $arrayNormalizer,
            $dateTimeNormalizer,
        ], [$jsonEncoder, $csvEncoder]);
    }

    //    public function getSerializerArray(): Serializer
    //    {
    //        return new Serializer(
    //            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
    //            [new JsonEncoder()]
    //        );
    //    }

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
