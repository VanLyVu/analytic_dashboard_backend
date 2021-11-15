<?php


namespace App\Dto\Request;


use App\Enum\ReportDateGroup;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;

class HotelReportFilterRequest
{
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
            'Y-m-d', $request->query->get('date_from')
        )->setTime(0,0,0);
        $this->dateTo = DateTime::createFromFormat(
            'Y-m-d', $request->query->get('date_to')
        )->setTime(23,59,59);

        $this->dateGroup = $this->getReportDateGroup($this->dateFrom, $this->dateTo);

    }

    private function getReportDateGroup(DateTime $dateFrom, DateTime $dateTo): string
    {
        $numberDateDiff = (int) $dateTo->diff($dateFrom)->format('%a');

        if ($numberDateDiff > 89) {
            return ReportDateGroup::MONTHLY;
        }

        if ($numberDateDiff > 29) {
            return ReportDateGroup::WEEKLY;
        }

        return ReportDateGroup::DAILY;
    }
}