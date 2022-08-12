<?php

namespace  battleoase\battlecore\groupSystem\api;

class TimeFormat
{

    public function __construct(private int $years, private int $months, private int $days, private int $hours, private int $minutes, private int $seconds) {}

    /**
     * @return int
     */
    public function getYears(): int
    {
        return $this->years;
    }

    /**
     * @return int
     */
    public function getMonths(): int
    {
        return $this->months;
    }

    /**
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
    }

    /**
     * @return int
     */
    public function getHours(): int
    {
        return $this->hours;
    }

    /**
     * @return int
     */
    public function getMinutes(): int
    {
        return $this->minutes;
    }

    /**
     * @return int
     */
    public function getSeconds(): int
    {
        return $this->seconds;
    }

    /**
     * @return int
     */
    
    public function getTime(): int
    {
        $int = $this->getYears() * 31104000 + $this->getMonths() * 2592000 + $this->getDays() * 86400 + $this->getHours() * 3600 + $this->getMinutes() * 60 + $this->getSeconds();
        return $int;
    }

    /**
     * @return int
     */

    public function getAddTime(): int
    {
        if ($this->getTime() == 0) {
            return 0;
        }
        return $time = intval($this->getTime() + time());
    }

    /**
     * @return string
     */

    public function asString(): string
    {
        if ($this->getTime() == 0) {
            return "Permanent";
        }
        return ($this->getYears() != 0 ? strval($this->getYears())." Year(s), " : "").($this->getMonths() != 0 ? strval($this->getMonths())." Month(s), " : "") . ($this->getDays() != 0 ? strval($this->getDays())." Day(s), " : "") .($this->getHours() != 0 ? strval($this->getHours())." Hour(s), " : "") . ($this->getMinutes() != 0 ? strval($this->getMinutes())." Minute(s) " : "") . ($this->getSeconds() != 0 ? strval($this->getSeconds())." Second(s)" : "") ;
    }
}