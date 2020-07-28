<?php


namespace App\Dto\Input;


use App\Entity\Hotel;

class BenchmarkParamsDto
{
    /**
     * @var Hotel
     */
    private Hotel $hotel;

    /**
     * @var DateRangeDto
     */
    private DateRangeDto $dateRange;

    /**
     * BenchmarkParamsDto constructor.
     * @param Hotel $hotel
     * @param DateRangeDto $dateRange
     */
    public function __construct(Hotel $hotel, DateRangeDto $dateRange)
    {
        $this->hotel = $hotel;
        $this->dateRange = $dateRange;
    }

    /**
     * @return Hotel
     */
    public function getHotel(): Hotel
    {
        return $this->hotel;
    }

    /**
     * @return DateRangeDto
     */
    public function getDateRange(): DateRangeDto
    {
        return $this->dateRange;
    }
}