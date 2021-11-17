<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\HotelReportFilterRequest;
use App\Dto\Response\HotelDto;
use App\Dto\Response\ReviewReportDto;
use App\Dto\Response\Transformer\HotelDtoTransformer;
use App\Dto\Response\Transformer\ReviewReportDtoTransformer;
use App\Enum\ReportDateGroup;
use App\Repository\HotelRepository;
use App\Repository\ReviewRepository;
use App\Utils\Constants;
use App\Utils\DateTimeUtils;
use DateInterval;
use DateTime;

class HotelReportService
{
    private HotelRepository $hotelRepository;

    private ReviewRepository $reviewRepository;

    private ReviewReportDtoTransformer $reviewReportDtoTransformer;

    /**
     * @var HotelDtoTransformer $hotelDtoTransformer
     */
    private HotelDtoTransformer $hotelDtoTransformer;

    public function __construct(
        HotelRepository $hotelRepository,
        ReviewRepository $reviewRepository,
        HotelDtoTransformer $hotelDtoTransformer,
        ReviewReportDtoTransformer $reviewReportDtoTransformer
    ) {
        $this->hotelRepository = $hotelRepository;
        $this->reviewRepository = $reviewRepository;
        $this->hotelDtoTransformer = $hotelDtoTransformer;
        $this->reviewReportDtoTransformer = $reviewReportDtoTransformer;
    }

    /**
     * @return HotelDto[]
     */
    public function getHotels(): array
    {
        return $this->hotelDtoTransformer->transformFromObjects(
            $this->hotelRepository->findAll()
        );
    }

    /**
     * @param HotelReportFilterRequest $hotelReportFilterRequest
     * @return ReviewReportDto
     * @throws \Exception
     */
    public function getHotelReport(HotelReportFilterRequest $hotelReportFilterRequest): ReviewReportDto
    {
        $reviewReportData = $this->reviewRepository->filterBy($hotelReportFilterRequest);

        $reviewReportData = $this->fillEmptyReport($reviewReportData, $hotelReportFilterRequest);

        return $this->reviewReportDtoTransformer
            ->setHotelReportFilterRequest($hotelReportFilterRequest)
            ->transformFromObject($reviewReportData);
    }

    /**
     * @param array $reviewReportData
     * @param HotelReportFilterRequest $hotelReportFilterRequest
     * @return array
     * @throws \Exception
     */
    private function fillEmptyReport(array $reviewReportData, HotelReportFilterRequest $hotelReportFilterRequest): array
    {
        $dataIndex = 0;
        $outputData = [];

        $dateFrom = $hotelReportFilterRequest->dateFrom;
        $timeInterval = "P1D";
        switch ($hotelReportFilterRequest->dateGroup) {
            case ReportDateGroup::WEEKLY:
                $dateFrom = DateTimeUtils::firstDayOfWeek($dateFrom);
                $timeInterval = "P7D";
                break;
            case ReportDateGroup::MONTHLY:
                $dateFrom = DateTimeUtils::firstDayOfMonth($dateFrom);
                $timeInterval = "P1M";
        }

        /** @var DateTime $dateFromCopy */
        /** @var DateTime $dateToCopy */
        [$dateFromCopy, $dateToCopy] = DateTimeUtils::cloneAndResetTime($dateFrom, $hotelReportFilterRequest->dateTo);

        while (0 <= DateTimeUtils::daysDiff($dateToCopy, $dateFromCopy)) {
            $data = isset($reviewReportData[$dataIndex]) ? $reviewReportData[$dataIndex] : null;

            if ($data && DateTimeUtils::daysDiff(new DateTime($data['date']), $dateFromCopy) == 0) {
                $outputData[] = $data;
                $dataIndex++;
            } else {
                $outputData[] = [
                    "hotel_id" => $hotelReportFilterRequest->hotelId,
                    "date" => $dateFromCopy->format(Constants::DATE_FORMAT),
                    "review_count" => 0,
                    "average_score" => null
                ];
            }
            $dateFromCopy->add(new DateInterval($timeInterval));
        }

        return $outputData;
    }
}