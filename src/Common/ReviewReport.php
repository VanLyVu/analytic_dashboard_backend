<?php

declare(strict_types=1);

namespace App\Common;


use App\Dto\Request\HotelReportFilterRequest;
use App\Enum\ReportDateGroup;
use App\Utils\DateTimeUtils;
use DateInterval;
use DateTime;

class ReviewReport
{
    public int $hotelId;

    /** @var DateTime  */
    public DateTime $dateFrom;

    /** @var DateTime  */
    public DateTime $dateTo;

    public string $dateGroup;

    /** @var ReviewDate[] */
    public array $reviewDates;

    public function __construct(
        int $hotelId, DateTime $dateFrom, DateTime $dateTo, string $dateGroup, array $reviewDates = []
    ) {
        $this->hotelId = $hotelId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->dateGroup = $dateGroup;
        $this->reviewDates = $reviewDates;
    }

    public static function createFromRequest(HotelReportFilterRequest $hotelReportFilterRequest): self
    {
        return new self(
            $hotelReportFilterRequest->hotelId,
            $hotelReportFilterRequest->dateFrom,
            $hotelReportFilterRequest->dateTo,
            $hotelReportFilterRequest->dateGroup
        );
    }

    public function setReviewDate(array $reviewDates): self
    {
        $this->reviewDates = $reviewDates;

        return $this;
    }

    public function getDateGroupStartDate(string $dateGroup, DateTime $dateFrom): DateTime
    {
        if ($dateGroup == ReportDateGroup::MONTHLY) {
            return DateTimeUtils::firstDayOfMonth($dateFrom);
        }

        if ($dateGroup == ReportDateGroup::WEEKLY) {
            return DateTimeUtils::firstDayOfWeek($dateFrom);
        }

        return clone $dateFrom;
    }

    public function getDateGroupInterval(string $dateGroup): DateInterval
    {
        if ($dateGroup == ReportDateGroup::MONTHLY) {
            return new DateInterval('P1M');
        }

        if ($dateGroup == ReportDateGroup::WEEKLY) {
            return new DateInterval('P7D');
        }

        return new DateInterval('P1D');
    }

}