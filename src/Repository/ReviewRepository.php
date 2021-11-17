<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\ReviewDate;
use App\Dto\Request\HotelReportFilterRequest;
use App\Entity\Review;
use App\Enum\ReportDateGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @param HotelReportFilterRequest $hotelReportFilterRequest
     * @return ReviewDate[]
     */
    public function filterBy(HotelReportFilterRequest $hotelReportFilterRequest): array
    {
        $query = $this->createQueryBuilder('r')
            ->select(
                'IDENTITY(r.hotel) as hotel_id',
                $this->selectDateQueryForReport($hotelReportFilterRequest->dateGroup),
                'COUNT(r.hotel) as review_count',
                'AVG(r.score) as average_score'
            )
            ->andWhere('r.hotel = :hotelId')
            ->setParameter('hotelId', $hotelReportFilterRequest->hotelId)
            ->andWhere('r.created_date >= :startDate')
            ->setParameter('startDate', $hotelReportFilterRequest->dateFrom)
            ->andWhere('r.created_date <= :endDate')
            ->setParameter('endDate', $hotelReportFilterRequest->dateTo)
            ->groupBy('r.hotel')
            ->addGroupBy('date')
            ->orderBy('date', 'ASC')
            ->getQuery();

        $reviewDates = [];

        foreach ($query->toIterable() as $record) {
            $reviewDates[] = new ReviewDate(
                (int) $record['hotel_id'],
                $record['date'],
                (int) $record['review_count'],
                (float) $record['average_score']
            );
        }

        return $reviewDates;

    }

    private function selectDateQueryForReport(string $dateGroup): string
    {
        switch ($dateGroup) {
            case ReportDateGroup::DAILY:
                return 'date(r.created_date) as date';
            case ReportDateGroup::WEEKLY:
                return "date(datesub(r.created_date, weekday(r.created_date), 'day')) as date";
            case ReportDateGroup::MONTHLY:
            default:
                return "date(dateadd(datesub(r.created_date, day(r.created_date), 'day'), 1, 'day')) as date";

        }
    }
}
