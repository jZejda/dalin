<?php

namespace App\Http\Controllers\Cron;

class CronTabManager
{
    public ?int $minutes;
    public ?int $hours;
    public ?int $dayOfMonth;
    public ?int $months;
    public ?int $dayOfWeek;

    public function __construct(?int $minutes = null, ?int $hours  = null, ?int $dayOfMonth  = null, ?int $months  = null, ?int $dayOfWeek  = null)
    {
        $this->minutes = $minutes;
        $this->hours = $hours;
        $this->dayOfMonth = $dayOfMonth;
        $this->months = $months;
        $this->dayOfWeek = $dayOfWeek;
    }


    public function getPass()
    {

        return $this->getHours();



    }

    public function getMinutes(): ?int
    {
        return $this->minutes;
    }

    /**
     * @param int|null $minutes
     */
    public function setMinutes(?int $minutes): void
    {
        $this->minutes = $minutes;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    /**
     * @param int|null $hours
     */
    public function setHours(?int $hours): void
    {
        $this->hours = $hours;
    }

    public function getDayOfMonth(): ?int
    {
        return $this->dayOfMonth;
    }

    /**
     * @param int|null $dayOfMonth
     */
    public function setDayOfMonth(?int $dayOfMonth): self
    {
        $this->dayOfMonth = $dayOfMonth;
    }

    public function getMonths(): ?int
    {
        return $this->months;
    }

    /**
     * @param int|null $months
     */
    public function setMonths(?int $months): self
    {
        $this->months = $months;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    /**
     * @param int|null $dayOfWeek
     */
    public function setDayOfWeek(?int $dayOfWeek): void
    {
        $this->dayOfWeek = $dayOfWeek;
    }


}
