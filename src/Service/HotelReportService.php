<?php

declare(strict_types=1);

namespace App\Service;

use App\Common\ReviewDate;
use App\Common\ReviewReport;
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
        $reviewDates = $this->reviewRepository->filterBy($hotelReportFilterRequest);
        $reviewReport = ReviewReport::createFromRequest($hotelReportFilterRequest);

        $reviewDates = $this->fillEmptyReport($reviewDates, $reviewReport);
        $reviewReport->setReviewDate($reviewDates);

        return $this->reviewReportDtoTransformer->transformFromObject($reviewReport);
    }

    /**
     * @param ReviewDate[] $reviewReportData
     * @param ReviewReport $reviewReport
     * @return ReviewDate[]
     * @throws \Exception
     */
    public function fillEmptyReport(array $reviewReportData, ReviewReport $reviewReport): array
    {
        $dataIndex = 0;
        $outputData = [];

        /** @var DateTime $dateFromCopy */
        /** @var DateTime $dateToCopy */
        [$dateFromCopy, $dateToCopy] = DateTimeUtils::cloneAndResetTime(
            $reviewReport->getDateGroupStartDate($reviewReport->dateGroup, $reviewReport->dateFrom),
            $reviewReport->dateTo
        );

        while (0 <= DateTimeUtils::daysDiff($dateToCopy, $dateFromCopy)) {
            $data = isset($reviewReportData[$dataIndex]) ? $reviewReportData[$dataIndex] : null;

            if ($data && DateTimeUtils::daysDiff(new DateTime($data->date), $dateFromCopy) == 0) {
                $outputData[] = $data;
                $dataIndex++;
            } else {
                $outputData[] = new ReviewDate(
                    $reviewReport->hotelId,
                    $dateFromCopy->format(Constants::DATE_FORMAT)
                );
            }
            $dateFromCopy->add(
                $reviewReport->getDateGroupInterval($reviewReport->dateGroup)
            );
        }

        return $outputData;
    }


}