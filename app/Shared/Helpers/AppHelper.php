<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

use App\Enums\AppRoles;
use App\Models\SportEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

final class AppHelper
{
    public const int GENERATED_PASSWORD_LENGTH = 12;

    public const string DATE_TIME_FORMAT = 'd.m.Y H:i';

    public const string DATE_TIME_FULL_FORMAT = 'd.m.Y H:i:s';

    public const string DATE_FORMAT = 'd.m.Y';

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

    /**
     * U závodů, které nepoužívají ORIS přihlášky, mohou pověřené role
     * přihlašovat a odhlašovat závodníky i po termínu přihlášek.
     */
    public static function allowModifyUserEntryAfterDeadline(SportEvent $sportEvent): bool
    {
        if ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
            return false;
        }

        return Auth::user()?->hasRole([
            AppRoles::SuperAdmin,
            AppRoles::EventMaster,
            AppRoles::EventOrganizer,
        ]) ?? false;
    }

    public static function getPageHelpUrl(string $finalUriPage): string
    {
        return 'https://jirizejda.cz/dalin/napoveda/'.$finalUriPage;
    }
}
