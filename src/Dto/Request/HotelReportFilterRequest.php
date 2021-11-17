<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Enum\ReportDateGroup;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;

class HotelReportFilterRequest
{
    const MONTH_GROUP_MIN_DAY = 90;

    const WEEK_GROUP_MIN_DAY = 30;

    public int $hotelId;

    /** @var DateTime  */
    public DateTime $dateFrom;

    /** @var DateTime  */
    public DateTime $dateTo;

    public string $dateGroup;

    public function __construct(Request $request)
    {
        $this->hotelId = (int) $request->query->get('hotel_id');
        $this->dateFrom = DateTime::createFromFormat(
            Constants::DATE_FORMAT, $request->query->get('date_from')
        )->setTime(0,0,0);
        $this->dateTo = DateTime::createFromFormat(
            Constants::DATE_FORMAT, $request->query->get('date_to')
        )->setTime(23,59,59);

        $this->dateGroup = $this->getReportDateGroup($this->dateFrom, $this->dateTo);

    }

    private function getReportDateGroup(DateTime $dateFrom, DateTime $dateTo): string
    {
        $numberDateDiff = $dateTo->diff($dateFrom)->days;

        if ($numberDateDiff >= self::MONTH_GROUP_MIN_DAY) {
            return ReportDateGroup::MONTHLY;
        }

        if ($numberDateDiff >= self::WEEK_GROUP_MIN_DAY) {
            return ReportDateGroup::WEEKLY;
        }

        return ReportDateGroup::DAILY;
    }
}