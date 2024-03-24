<?php

declare(strict_types=1);

namespace App\Helpers;

class LeafLetMapHelpers
{
    public static function getCenterOfCoords(array $coords): array
    {
        $count_coords = count($coords);
        $xCos = 0.0;
        $yCos = 0.0;
        $zSin = 0.0;

        foreach ($coords as $lngLat) {
            $lat = $lngLat['lat'] * pi() / 180;
            $lon = $lngLat['lng'] * pi() / 180;

            $acos = cos($lat) * cos($lon);
            $bCos = cos($lat) * sin($lon);
            $cSin = sin($lat);
            $xCos += $acos;
            $yCos += $bCos;
            $zSin += $cSin;
        }

        $xCos /= $count_coords;
        $yCos /= $count_coords;
        $zSin /= $count_coords;
        $lon = atan2($yCos, $xCos);
        $sqrt = sqrt($xCos * $xCos + $yCos * $yCos);
        $lat = atan2($zSin, $sqrt);

        return [$lat * 180 / pi(), $lon * 180 / pi()];
    }

}
