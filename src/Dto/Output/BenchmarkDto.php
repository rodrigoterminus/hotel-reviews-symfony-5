<?php


namespace App\Dto\Output;


class BenchmarkDto
{
    /**
     * @var float
     */
    private float $hotelAverage;

    /**
     * @var float
     */
    private float $totalAverage;

    /**
     * @var string|null
     */
    private ?string $quarter;

    /**
     * BenchmarkDto constructor.
     * @param float $hotelAverage
     * @param float $totalAverage
     * @param string $quarter
     */
    public function __construct(float $hotelAverage, float $totalAverage, ?string $quarter)
    {
        $this->hotelAverage = $hotelAverage;
        $this->totalAverage = $totalAverage;
        $this->quarter = $quarter;
    }

    /**
     * @return float
     */
    public function getHotelAverage(): float
    {
        return $this->hotelAverage;
    }

    /**
     * @return float
     */
    public function getTotalAverage(): float
    {
        return $this->totalAverage;
    }

    /**
     * @return string
     */
    public function getQuarter()
    {
        return $this->quarter;
    }

}