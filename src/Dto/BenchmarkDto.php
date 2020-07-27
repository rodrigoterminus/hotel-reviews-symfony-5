<?php


namespace App\Dto;


class BenchmarkDto
{
    private float $hotelAverage;

    private float $average;

    private $quarter;

    public function __construct($hotelAverage, $average, $quarter)
    {
        $this->hotelAverage = $hotelAverage;
        $this->average = $average;
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
    public function getAverage(): float
    {
        return $this->average;
    }

    /**
     * @return string
     */
    public function getQuarter()
    {
        return $this->quarter;
    }

}