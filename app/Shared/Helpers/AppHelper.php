<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

use App\Models\SportEvent;
use Illuminate\Support\Carbon;

final class AppHelper
{
    public const string DATE_TIME_FORMAT = 'd. m. Y H:i';

    public const string DATE_TIME_FULL_FORMAT = 'd. m. Y H:i:s';

    public const string DATE_FORMAT = 'd. m. Y';

    public const string MYSQL_DATE_TIME = 'Y-m-d H:i:s';

    public const string DB_DATE_TIME = 'Y-m-d';

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

    public static function getWhiteSpaceBeforeString(?string $characters, int $totalLength): string
    {
        if (EmptyType::stringNotEmpty($characters) && ! is_null($characters)) {
            $stringLength = mb_strlen($characters);

            $string = $characters;
            for ($i = 0; $i < $totalLength - $stringLength; $i++) {
                $string = $string.'&nbsp;';
            }
        } else {
            $string = '';
        }

        return $string;
    }

    public static function allowModifyUserEntry(SportEvent $sportEvent): bool
    {
        if (is_null($sportEvent->lastEntryDate())) {
            return false;
        }

        $lastEntryDate = Carbon::createFromFormat(self::MYSQL_DATE_TIME, $sportEvent->lastEntryDate()->format(self::MYSQL_DATE_TIME));
        if ($lastEntryDate !== false && $lastEntryDate !== null) {
            return $lastEntryDate->lte(Carbon::now());
        }

        return false;
    }

    public static function getPageHelpUrl(string $finalUriPage): string
    {
        return 'https://jirizejda.cz/dalin/napoveda/'.$finalUriPage;
    }
}
