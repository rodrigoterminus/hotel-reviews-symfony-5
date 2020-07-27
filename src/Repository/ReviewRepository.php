<?php

namespace App\Repository;

use App\Dto\OvertimeDto;
use App\Entity\Hotel;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->groupBy('group')
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
     * @param Hotel|null $hotel
     * @param array $dateRange
     * @return array
     */
    public function getAverageScore(array $dateRange = [], Hotel $hotel = null): array
    {
        $resolver = (new OptionsResolver())
            ->setDefaults([
                'starting' => null,
                'ending' => null,
            ])
            ->setAllowedTypes('starting', 'DateTime')
            ->setAllowedTypes('ending', 'DateTime');
        $range = $resolver->resolve($dateRange);

        $qb = $this->createQueryBuilder('review');
        $qb->select('AVG(review.score) AS average_score')
            ->addSelect('IDENTITY(review.hotel) AS hotel_id')
            ->groupBy('review.hotel');

        if ($hotel) {
            $qb
                ->andWhere($qb->expr()->eq('review.hotel', ':hotel'))
                ->setParameter(':hotel', $hotel);
        }

        if ($range['starting']) {
            $qb->andWhere($qb->expr()->gte('review.created_date', ':startingDate'))
                ->setParameter(':startingDate', $range['starting']);
        }

        if ($range['ending']) {
            $qb->andWhere($qb->expr()->lte('review.created_date', ':endingDate'))
                ->setParameter(':endingDate', $range['ending']);
        }

        return $qb->getQuery()->getResult();
    }
}
