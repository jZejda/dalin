<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron;

use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Carbon;

final class CronTabManager
{
    public array $hours;
    public array $daysInMonth;
    public array $months;
    public array $daysInWeek;

    public function __construct(array $hours, array $daysInMonth, array $months, array $daysInWeek)
    {
        $this->hours = $hours;
        $this->daysInMonth = $daysInMonth;
        $this->months = $months;
        $this->daysInWeek = $daysInWeek;
    }

    public function jobRunner(): bool
    {
        if (
            $this->checkHour() &&
            $this->checkDaysInMonth() &&
            $this->checkMonths() &&
            $this->checkDaysInWeek()
        ) {
            return true;
        }

        return false;
    }

    private function checkHour(): bool
    {
        $actualHour = Carbon::now()->format('H');
        return $this->isInArray($this->hours, $actualHour);
    }

    private function checkDaysInMonth(): bool
    {
        $actualDayInMonth = Carbon::now()->format('d');
        return $this->isInArray($this->daysInMonth, $actualDayInMonth);
    }

    private function checkMonths(): bool
    {
        $actualMonth = Carbon::now()->format('m');
        return $this->isInArray($this->months, $actualMonth);
    }

    private function checkDaysInWeek(): bool
    {
        $actualDayInWeek = Carbon::now()->dayOfWeek;
        return $this->isInArray($this->daysInWeek, (string)$actualDayInWeek);
    }

    private function isInArray(array $array, string $checkValue): bool
    {
        if (in_array($checkValue, $array, true)) {
            return true;
        }

        if (EmptyType::arrayEmpty($array)) {
            return true;
        }

        if ($array[0] === '*') {
            return true;
        }

        return false;
    }
}
