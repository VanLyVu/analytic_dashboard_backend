<?php

declare(strict_types=1);

namespace App\Dto\Response\Transformer;


use App\Dto\Request\HotelReportFilterRequest;
use App\Dto\Response\ReviewReportDto;

class ReviewReportDtoTransformer extends AbstractResponseDtoTransformer
{
    /** @var HotelReportFilterRequest */
    private HotelReportFilterRequest $hotelReportFilterRequest;

    private ReviewDateDtoTransformer $reviewDateDtoTransformer;

    public function __construct(ReviewDateDtoTransformer $reviewDateDtoTransformer)
    {
        $this->reviewDateDtoTransformer = $reviewDateDtoTransformer;
    }

    public function transformFromObject($data)
    {
        if (is_null($this->hotelReportFilterRequest)) {
            return [];
        }

        $reviewReportDto = new ReviewReportDto();
        $reviewReportDto->hotel_id = $this->hotelReportFilterRequest->hotelId;
        $reviewReportDto->date_from = $this->hotelReportFilterRequest->dateFrom->format('Y-m-d');
        $reviewReportDto->date_to = $this->hotelReportFilterRequest->dateTo->format('Y-m-d');
        $reviewReportDto->date_group = $this->hotelReportFilterRequest->dateGroup;
        $reviewReportDto->review_dates = $this->reviewDateDtoTransformer->transformFromObjects($data);

        return $reviewReportDto;
    }

    public function setHotelReportFilterRequest(HotelReportFilterRequest $hotelReportFilterRequest): self
    {
        $this->hotelReportFilterRequest = $hotelReportFilterRequest;

        return $this;
    }
}