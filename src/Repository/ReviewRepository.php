<?php

namespace App\Repository;

use App\Dto\OvertimeDto;
use App\Entity\Hotel;
use App\Entity\Review;
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
    const GROUP_DAILY = 'daily';
    const GROUP_WEEKLY = 'weekly';
    const GROUP_MONTHLY = 'monthly';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function getAverageByDateRange(
        \DateTime $startingDate,
        \DateTime $endingDate,
        Hotel $hotel = null,
        string $grouping = null
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
            ->setParameters([
                ':startingDate' => $startingDate,
                ':endingDate' => $endingDate,
            ]);

        if ($hotel) {
            $qb
                ->andWhere(
                    $qb->expr()->eq('review.hotel', ':hotel')
                )
                ->setParameter(':hotel', $hotel);
        }

        switch ($grouping) {
            case self::GROUP_DAILY:
                $qb->addSelect('review.created_date AS group')
                    ->groupBy('group');
                break;

            case self::GROUP_WEEKLY:
                $weekGroup = 'CONCAT(YEAR(review.created_date), \'-CW\', WEEK(review.created_date))';
                $qb->addSelect($weekGroup . ' AS group')
                    ->groupBy('group');
                break;

            case self::GROUP_MONTHLY:
                $monthGroup = 'CONCAT(YEAR(review.created_date), \'-\', MONTH(review.created_date))';
                $qb->addSelect($monthGroup . ' AS group')
                    ->groupBy('group');
                break;
        }

        return $qb->getQuery()->getResult();
    }
}
