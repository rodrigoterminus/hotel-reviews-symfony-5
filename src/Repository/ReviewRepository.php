<?php

namespace App\Repository;


use App\Dto\Input\DateRangeDto;
use App\Entity\Hotel;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    const GROUP_DAILY = 'daily';
    const GROUP_WEEKLY = 'weekly';
    const GROUP_MONTHLY = 'monthly';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @param DateRangeDto $dateRange
     * @param Hotel $hotel
     * @param string $grouping
     * @return array
     */
    public function getAverageScoreByDateRange(
        DateRangeDto $dateRange,
        Hotel $hotel,
        string $grouping
    ): array
    {
        $qb = $this->createQueryBuilder('review');
        $qb
            ->select('COUNT(review.id) AS count')
            ->addSelect('AVG(review.score) AS average')
            ->where(
                $qb->expr()->between(
                    'review.created_date',
                    ':startingDate',
                    ':endingDate',
                )
            )
            ->andWhere($qb->expr()->eq('review.hotel', ':hotel'))
            ->groupBy('group')
            ->setParameters([
                ':startingDate' => $dateRange->getStartingDate(),
                ':endingDate' => $dateRange->getEndingDate(),
                ':hotel' => $hotel,
            ]);

        switch ($grouping) {
            case self::GROUP_DAILY:
                $dayGroup = 'DATE_FORMAT(review.created_date, \'%Y-%m-%d\')';
                $qb->addSelect($dayGroup . ' AS group');
                break;

            case self::GROUP_WEEKLY:
                $weekGroup = 'CONCAT(YEAR(review.created_date), \'-CW\', WEEK(review.created_date))';
                $qb->addSelect($weekGroup . ' AS group');
                break;

            case self::GROUP_MONTHLY:
                $monthGroup = 'DATE_FORMAT(review.created_date, \'%Y-%m\')';
                $qb->addSelect($monthGroup . ' AS group');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param DateRangeDto $dateRange
     * @return array
     */
    public function getAverageScorePerHotel(DateRangeDto $dateRange): array
    {
        $qb = $this->createQueryBuilder('review');
        $qb->select('AVG(review.score) AS average_score')
            ->addSelect('IDENTITY(review.hotel) AS hotel_id')
            ->groupBy('review.hotel');

        if ($dateRange->getStartingDate()) {
            $qb->andWhere($qb->expr()->gte('review.created_date', ':startingDate'))
                ->setParameter(':startingDate', $dateRange->getStartingDate());
        }

        if ($dateRange->getEndingDate()) {
            $qb->andWhere($qb->expr()->lte('review.created_date', ':endingDate'))
                ->setParameter(':endingDate', $dateRange->getEndingDate());
        }

        return $qb->getQuery()->getResult();
    }
}
