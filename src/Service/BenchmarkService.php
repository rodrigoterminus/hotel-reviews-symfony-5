<?php


namespace App\Service;


use App\Dto\BenchmarkDto;
use App\Entity\Hotel;
use App\Repository\ReviewRepository;
use App\Util\Statistics;

class BenchmarkService
{
    const BENCHMARK_TIER_BOTTOM = 'bottom';
    const BENCHMARK_TIER_TOP = 'top';

    /**
     * @var ReviewRepository
     */
    private ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function generate(Hotel $hotel, \DateTime $startingDate, \DateTime $endingDate): BenchmarkDto
    {
        $result =  $this->reviewRepository->getAverageScore(
            [
                'starting' => $startingDate,
                'ending' => $endingDate,
            ],
        );

        $averageScores = $this->normalizeResult($result);
        $averageScore = array_sum($averageScores) / count($averageScores);
        $hotelAverageScore = $averageScores[$hotel->getId()];
        $quartiles = Statistics::quartiles($averageScores);
        $tier = $this->getTier($quartiles, $hotelAverageScore);

        return new BenchmarkDto($hotelAverageScore, $averageScore, $tier);
    }

    /**
     * @param array $result
     * @return array
     */
    private function normalizeResult(array $result): array
    {
        $averages = [];

        foreach ($result as $item) {
            $averages[$item['hotel_id']] = $item['average_score'];
        }

        return $averages;
    }

    private function getTier(array $quartiles, $number)
    {
        if ($number < $quartiles['first']) {
            return self::BENCHMARK_TIER_BOTTOM;
        } else if ($number > $quartiles['third']) {
            return self::BENCHMARK_TIER_TOP;
        }

        return null;
    }
}