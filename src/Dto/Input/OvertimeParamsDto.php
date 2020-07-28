<?php


namespace App\Dto\Input;


use App\Entity\Hotel;

class OvertimeParamsDto
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
     * OvertimeParamsDto constructor.
     * @param Hotel $hotel
     * @param DateRangeDto $dateRangeDto
     */
    public function __construct(Hotel $hotel, DateRangeDto $dateRangeDto)
    {
        $this->hotel = $hotel;
        $this->dateRange = $dateRangeDto;
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