<?php

declare(strict_types=1);

namespace App\Service;


use App\Dto\Request\HotelReportFilterRequest;
use App\Dto\Response\ReviewReportDto;
use App\Dto\Response\Transformer\HotelDtoTransformer;
use App\Dto\Response\Transformer\ReviewReportDtoTransformer;
use App\Entity\Hotel;
use App\Enum\ReportDateGroup;
use App\Repository\HotelRepository;
use App\Repository\ReviewRepository;
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
     * @return Hotel[]
     */
    public function getHotels(): array
    {
        return $this->hotelDtoTransformer->transformFromObjects(
            $this->hotelRepository->findAll()
        );
    }

    public function getHotelReport(HotelReportFilterRequest $hotelReportFilterRequest): ReviewReportDto
    {
        $reviewReportData = $this->reviewRepository->filterBy($hotelReportFilterRequest);
        return $this->reviewReportDtoTransformer
            ->setHotelReportFilterRequest($hotelReportFilterRequest)
            ->transformFromObject($reviewReportData);
    }
}