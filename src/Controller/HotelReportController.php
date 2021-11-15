<?php

//declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\HotelReportFilterRequest;
use App\Service\HotelReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HotelReportController extends AbstractController
{
    /**
     * @var HotelReportService $hotelReportService
     */
    private HotelReportService $hotelReportService;
    public function __construct(HotelReportService $hotelReportService)
    {
        $this->hotelReportService = $hotelReportService;
    }

    /**
     * @Route("/api/hotel_reports/hotels", name="hotel_report_hotels")
     */
    public function index(): Response
    {
        return $this->json($this->hotelReportService->getHotels());
    }

    /**
     * @Route("/api/hotel_reports/show", name="hotel_report_detail")
     */
    public function show(Request $request): Response
    {
        $hotelReportFilterRequest = new HotelReportFilterRequest($request);
        return $this->json(
            $this->hotelReportService->getHotelReport($hotelReportFilterRequest)
        );
    }
}
