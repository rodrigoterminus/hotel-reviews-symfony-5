<?php


namespace App\Dto\Output;


class OvertimeDto
{
    /**
     * @var int
     */
    private int $reviewCount;

    /**
     * @var float
     */
    private float $averageScore;

    /**
     * @var string
     */
    private string $dateGroup;

    /**
     * OvertimeDto constructor.
     * @param int $reviewCount
     * @param float $averageScore
     * @param string $dateGroup
     */
    public function __construct(int $reviewCount, float $averageScore, string $dateGroup)
    {
        $this->reviewCount = $reviewCount;
        $this->averageScore = $averageScore;
        $this->dateGroup = $dateGroup;
    }

    /**
     * @return int
     */
    public function getReviewCount(): int
    {
        return $this->reviewCount;
    }

    /**
     * @return int
     */
    public function getAverageScore(): int
    {
        return $this->averageScore;
    }

    /**
     * @return string
     */
    public function getDateGroup(): string
    {
        return $this->dateGroup;
    }
}