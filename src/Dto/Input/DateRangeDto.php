<?php


namespace App\Dto\Input;


class DateRangeDto
{
    /**
     * @var \DateTime
     */
    private \DateTime $startingDate;

    /**
     * @var \DateTime
     */
    private \DateTime $endingDate;

    /**
     * DateRangeDto constructor.
     * @param \DateTime $startingDate
     * @param \DateTime $endingDate
     */
    public function __construct(\DateTime $startingDate, \DateTime $endingDate)
    {
        $this->startingDate = $startingDate;
        $this->endingDate = $endingDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndingDate(): \DateTime
    {
        return $this->endingDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartingDate(): \DateTime
    {
        return $this->startingDate;
    }

}