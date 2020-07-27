<?php


namespace App\Dto;


use Symfony\Component\Serializer\Annotation\SerializedName;

class OvertimeDto
{
    /**
     * @var int
     * @SerializedName("review-count")
     */
    private $reviewCount;

    /**
     * @var int
     * @SerializedName("average-score")
     */
    private $averageScore;

    /**
     * @var string
     * @SerializedName("date-group")
     */
    private $dateGroup;

    public function __construct($reviewCount, $averageScore, $dateGroup)
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