<?php

declare(strict_types=1);

namespace App\Dto\Response\Transformer;

use App\Common\ReviewReport;
use App\Dto\Response\ReviewReportDto;
use App\Utils\Constants;

class ReviewReportDtoTransformer extends AbstractResponseDtoTransformer
{
    private ReviewDateDtoTransformer $reviewDateDtoTransformer;

    public function __construct(ReviewDateDtoTransformer $reviewDateDtoTransformer)
    {
        $this->reviewDateDtoTransformer = $reviewDateDtoTransformer;
    }

    /**
     * @param ReviewReport $reviewReport
     * @return ReviewReportDto
     */
    public function transformFromObject($reviewReport)
    {
        $reviewReportDto = new ReviewReportDto();
        $reviewReportDto->hotel_id = $reviewReport->hotelId;
        $reviewReportDto->date_from = $reviewReport->dateFrom->format(Constants::DATE_FORMAT);
        $reviewReportDto->date_to = $reviewReport->dateTo->format(Constants::DATE_FORMAT);
        $reviewReportDto->date_group = $reviewReport->dateGroup;
        $reviewReportDto->review_dates = $this->reviewDateDtoTransformer->transformFromObjects($reviewReport->reviewDates);

        return $reviewReportDto;
    }
}