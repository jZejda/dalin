<?php

declare(strict_types=1);

namespace App\Services;


use App\Models\SportEventExport;
use DB;
use App\Http\Components\Iofv3\StartList;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class IofExportsService
{
    public function getStartList(string $xmlContent): StartList
    {
        return $this->getSerializer()->deserialize(
            $xmlContent,
            'App\Http\Components\Iofv3\StartList',
            'xml'
        );
    }

    public function getResourceBySlug(?string $slug): ?string
    {
        /** @var ?SportEventExport $sportEventExport */
        $sportEventExport = DB::table('sport_event_exports')->where('slug', '=', $slug)->first();

        return $sportEventExport?->result_path;
    }

    private function getSerializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer(null, null, null, $extractor)];

        return new Serializer($normalizers, $encoders);
    }
}
