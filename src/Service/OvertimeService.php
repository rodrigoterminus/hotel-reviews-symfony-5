<?php


namespace App\Service;


use App\Dto\OvertimeDto;
use App\Entity\Hotel;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class OvertimeService
{
    const GROUP_DAILY_LIMIT = 29;
    const GROUP_WEEKLY_LIMIT = 89;

    /**
     * @var ReviewRepository
     */
    private ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function getByHotel(Hotel $hotel, \DateTime $startingDate, \DateTime $endingDate): array
    {
        $diffDays = $startingDate->diff($endingDate)->days;

        if ($diffDays <= self::GROUP_DAILY_LIMIT) {
            $grouping = ReviewRepository::GROUP_DAILY;
        } else if ($diffDays <= self::GROUP_WEEKLY_LIMIT) {
            $grouping = ReviewRepository::GROUP_WEEKLY;
        } else {
            $grouping = ReviewRepository::GROUP_MONTHLY;
        }

        $dtos = [];
        $result = $this->reviewRepository->getAverageByDateRange(
            $startingDate,
            $endingDate,
            $hotel,
            $grouping,
        );

        foreach ($result as $row) {
            $dtos[] = (new OvertimeDto())
                ->setAverageScore($row['average'])
                ->setReviewCount($row['count'])
                ->setDateGroup($row['group']);
        }

        return $dtos;
    }
}