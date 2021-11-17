<?php

namespace App\Tests\Unit\Service;

use App\Common\ReviewDate;
use App\Common\ReviewReport;
use App\Dto\Response\Transformer\HotelDtoTransformer;
use App\Dto\Response\Transformer\ReviewReportDtoTransformer;
use App\Enum\ReportDateGroup;
use App\Repository\HotelRepository;
use App\Repository\ReviewRepository;
use App\Service\HotelReportService;
use App\Tests\TestCase;
use DateTime;

class HotelReportServiceTest extends TestCase
{
    /**
     * @test
     */
    public function fill_empty_reports_for_daily(): void
    {
        // Arrange
        $reviewDates = [
            new ReviewDate(1, '2021-11-10', 1, 1.0),
            new ReviewDate(1, '2021-11-11'),
            new ReviewDate(1, '2021-11-13', 2, 3.0),
        ];

        $reviewReport = new ReviewReport(
            1, new DateTime('2021-11-09'), new DateTime('2021-11-14'), ReportDateGroup::DAILY
        );

        // Act
        $service = $this->createServiceWithDummyObject();
        $expected = $this->invokeMethod(
            $service,
            'fillEmptyReport',
            [ $reviewDates, $reviewReport]
        );

        // Assert
        $this->assertEquals($expected, [
            new ReviewDate(1, '2021-11-09'),
            new ReviewDate(1, '2021-11-10', 1, 1.0),
            new ReviewDate(1, '2021-11-11'),
            new ReviewDate(1, '2021-11-12'),
            new ReviewDate(1, '2021-11-13', 2, 3.0),
            new ReviewDate(1, '2021-11-14'),
        ]);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function fill_empty_reports_for_weekly(): void
    {
        // Arrange
        $reviewDates = [
            new ReviewDate(1, '2021-11-08', 1, 1.0),
            new ReviewDate(1, '2021-11-15'),
            new ReviewDate(1, '2021-11-29', 2, 3.0),
        ];

        $reviewReport = new ReviewReport(
            1, new DateTime('2021-11-02'), new DateTime('2021-12-07'), ReportDateGroup::WEEKLY
        );

        // Act
        $service = $this->createServiceWithDummyObject();
        $expected = $this->invokeMethod(
            $service,
            'fillEmptyReport',
            [ $reviewDates, $reviewReport]
        );

        // Assert
        $this->assertEquals($expected, [
            new ReviewDate(1, '2021-11-01'),
            new ReviewDate(1, '2021-11-08', 1, 1.0),
            new ReviewDate(1, '2021-11-15'),
            new ReviewDate(1, '2021-11-22'),
            new ReviewDate(1, '2021-11-29', 2, 3.0),
            new ReviewDate(1, '2021-12-06'),
        ]);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function fill_empty_reports_for_monthly(): void
    {
        // Arrange
        $reviewDates = [
            new ReviewDate(1, '2021-09-01', 1, 1.0),
            new ReviewDate(1, '2021-11-01'),
        ];

        $reviewReport = new ReviewReport(
            1, new DateTime('2021-08-31'), new DateTime('2021-12-07'), ReportDateGroup::MONTHLY
        );

        // Act
        $service = $this->createServiceWithDummyObject();
        $expected = $this->invokeMethod(
            $service,
            'fillEmptyReport',
            [ $reviewDates, $reviewReport]
        );

        // Assert
        $this->assertEquals($expected, [
            new ReviewDate(1, '2021-08-01'),
            new ReviewDate(1, '2021-09-01', 1, 1.0),
            new ReviewDate(1, '2021-10-01'),
            new ReviewDate(1, '2021-11-01'),
            new ReviewDate(1, '2021-12-01'),
        ]);
    }

    protected function createServiceWithDummyObject(): HotelReportService
    {
        return new HotelReportService(
            $this->createMock(HotelRepository::class),
            $this->createMock(ReviewRepository::class),
            $this->createMock(HotelDtoTransformer::class),
            $this->createMock(ReviewReportDtoTransformer::class)
        );

    }


}
