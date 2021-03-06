<?php


namespace App\Service;


use App\Dto\Output\BenchmarkDto;
use App\Dto\Input\BenchmarkParamsDto;
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

    /**
     * Generate benchmark for a given Hotel
     *
     * @param BenchmarkParamsDto $params
     * @return BenchmarkDto|null
     */
    public function generate(BenchmarkParamsDto $params): ?BenchmarkDto
    {
        $result = $this->reviewRepository->getAverageScorePerHotel($params->getDateRange());

        if (count($result) === 0) {
            return null;
        }

        $averageScores = $this->normalizeResult($result);
        $averageScore = array_sum($averageScores) / count($averageScores);
        $hotelAverageScore = $averageScores[$params->getHotel()->getId()];

        if (count($averageScores) < 2) {
            return new BenchmarkDto($hotelAverageScore, $averageScore, null);
        }

        $quartiles = Statistics::quartiles($averageScores);
        $tier = $this->getTier($quartiles, $hotelAverageScore);

        return new BenchmarkDto($hotelAverageScore, $averageScore, $tier);
    }

    /**
     * Transform results into associative array where keys are hotel_id and values are the average scores
     *
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

    /**
     * Return which tier a number belongs to given a quartile
     *
     * @param array $quartiles
     * @param $number
     * @return string|null
     */
    private function getTier(array $quartiles, $number): ?string
    {
        if ($number < $quartiles['first']) {
            return self::BENCHMARK_TIER_BOTTOM;
        } else if ($number > $quartiles['third']) {
            return self::BENCHMARK_TIER_TOP;
        }

        return null;
    }
}