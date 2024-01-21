<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Components\Iofv3\ResultList;
use App\Http\Components\Oris\GetOris;
use App\Services\OrisApiService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TestController extends Controller
{
    public function test(): void
    {

        $getParams = [
            'method' => 'getEventStartLists',
            'eventid' => 7586,
            'clubid' => 1,
        ];

        $orisResponse = $this->orisGetResponse($getParams);

        $startList = new GetOris();

        if ($startList->checkOrisResponse($orisResponse)) {
            $orisData = $startList->startList($orisResponse);


            dd($orisData);
        }


    }


    public function getSerializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer(null, null, null, $extractor)];
        //$serializer = new Serializer($normalizer, $encoder);

        //        $result = $serializer->deserialize($data,someEntity::class,'json');
        //
        //
        //        $encoders = [new XmlEncoder(), new JsonEncoder()];
        //        $normalizers = [new ObjectNormalizer(), new GetSetMethodNormalizer(), new ArrayDenormalizer()];

        return new Serializer($normalizers, $encoders);
    }

    public function testTTT()
    {

        return (new UsersExport())->download('users.xlsx');


        return (new ExportController())->exportViaConstructorInjection();



    }

    private function getResult()
    {

        /**
         * @var ResultList $data
         */
        $data = $this->getSerializer()->deserialize(
            $data,
            'App\Http\Components\Iofv3\ResultList',
            'xml'
        );


    }

    private function orisGetResponse(array $getParams): Response
    {
        $params = array_merge_recursive(['format' => OrisApiService::ORIS_API_DEFAULT_FORMAT], $getParams);

        return Http::get(OrisApiService::ORIS_API_URL, $params)->throw();
    }

}
