<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

use Illuminate\Support\Carbon;

final class AppHelper
{
    public const DATE_TIME_FORMAT = 'd. m. Y H:i';
    public const DATE_TIME_FULL_FORMAT = 'd. m. Y H:i:s';
    public const DATE_FORMAT = 'd. m. Y';

    public const MYSQL_DATE_TIME = 'Y-m-d H:i:s';


    public function getDataEntryClassCollor(Carbon $dataTime): string
    {
        $now = Carbon::now();
        if ($now > $dataTime) {
            return 'text-danger-600';
        } elseif (($now->subDays(5) <= $dataTime) && ($dataTime <= $now)) {
            return 'text-warning-700';
        }

        return '';
    }

    public static function getWhiteSpaceBeforeString(string $characters, int $totalLength): string
    {
        $stringLength = mb_strlen($characters);

        $string = $characters;
        for ($i = 0; $i < $totalLength - $stringLength; $i++){
            $string = $string . '&nbsp;';
        }
        return $string;
    }

}
