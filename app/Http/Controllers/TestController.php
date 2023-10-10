<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\ExportController;
use App\Exports\UsersExport;
use App\Http\Components\Iofv3\ResultList;
use Illuminate\Support\Facades\Storage;
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
    public function testTTT(): void
    {

        //$file = Storage::disk('events')->get('storage/vysledky_iofv3.xml');
        $file = Storage::disk('events')->get('storage/startovka-kat-iof3-jirka.xml');

        // dd($file);
        //(new EntryEndsToPay())->run();
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

    public function test()
    {

        return (new UsersExport)->download('users.xlsx');


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

}
