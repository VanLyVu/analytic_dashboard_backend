<?php

declare(strict_types=1);

namespace App\Utils;

use DateInterval;
use DateTime;

class DateTimeUtils
{
    use StaticSingletonUtils;

    public function daysDiff(DateTime $dateOrigin, DateTime $dateCompareTo): int
    {
        /** @var \DateInterval $interval */
        $interval = $dateOrigin->diff($dateCompareTo);
        return $interval->invert == 0 ? - $interval->days : $interval->days;
    }

    public function firstDayOfWeek(DateTime $date): DateTime
    {
        $daysOfWeek = (int) $date->format('w') - 1;
        return (clone $date)->sub(new DateInterval("P{$daysOfWeek}D"));
    }

    public function firstDayOfMonth(DateTime $date): DateTime
    {
        $daysOfMonth = (int) $date->format('d') - 1;
        return (clone $date)->sub(new DateInterval("P{$daysOfMonth}D"));
    }

    /**
     * @static
     * @param mixed ...$dateParameters
     * @return array
     */
    public function cloneAndResetTime(...$dateParameters): array
    {
        $cloneDateOutput = [];
        /** @var DateTime $date */
        foreach ($dateParameters as $date) {
            $cloneDateOutput[] = (clone $date)->setTime(0,0,0);
        }

        return $cloneDateOutput;
    }
}