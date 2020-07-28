<?php


namespace App\Service;


use App\Dto\OvertimeDto;
use App\Entity\Hotel;
use App\Repository\ReviewRepository;

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

    /**
     * @param Hotel $hotel
     * @param \DateTime $startingDate
     * @param \DateTime $endingDate
     * @return array
     */
    public function getByHotel(Hotel $hotel, \DateTime $startingDate, \DateTime $endingDate): array
    {
        $grouping = $this->getGrouping($startingDate, $endingDate);
        $dtos = [];
        $result = $this->reviewRepository->getAverageScoreByDateRange(
            $startingDate,
            $endingDate,
            $hotel,
            $grouping,
        );

        foreach ($result as $row) {
            $dtos[] = (new OvertimeDto($row['count'], $row['average'], $row['group']));
        }

        return $dtos;
    }

    /**
     * Get date group by a given date range
     *
     * @param \DateTime $startingDate
     * @param \DateTime $endingDate
     * @return string
     */
    private function getGrouping(\DateTime $startingDate, \DateTime $endingDate)
    {
        $diffDays = $startingDate->diff($endingDate)->days;

        if ($diffDays <= self::GROUP_DAILY_LIMIT) {
            return ReviewRepository::GROUP_DAILY;
        } else if ($diffDays <= self::GROUP_WEEKLY_LIMIT) {
            return ReviewRepository::GROUP_WEEKLY;
        }

        return ReviewRepository::GROUP_MONTHLY;
    }
}