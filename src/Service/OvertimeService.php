<?php


namespace App\Service;


use App\Dto\Input\DateRangeDto;
use App\Dto\Input\OvertimeParamsDto;
use App\Dto\Output\OvertimeDto;
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
     * @param OvertimeParamsDto $params
     * @return array
     */
    public function getByHotel(OvertimeParamsDto $params): array
    {
        $grouping = $this->getGrouping($params->getDateRange());
        $dtos = [];
        $result = $this->reviewRepository->getAverageScoreByDateRange(
            $params->getDateRange(),
            $params->getHotel(),
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
     * @param DateRangeDto $dateRange
     * @return string
     */
    private function getGrouping(DateRangeDto $dateRange)
    {
        $diffDays = $dateRange->getStartingDate()
            ->diff($dateRange->getEndingDate())->days;

        if ($diffDays <= self::GROUP_DAILY_LIMIT) {
            return ReviewRepository::GROUP_DAILY;
        } else if ($diffDays <= self::GROUP_WEEKLY_LIMIT) {
            return ReviewRepository::GROUP_WEEKLY;
        }

        return ReviewRepository::GROUP_MONTHLY;
    }
}