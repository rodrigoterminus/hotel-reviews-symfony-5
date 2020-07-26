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

    /**
     * @return int
     */
    public function getReviewCount(): int
    {
        return $this->reviewCount;
    }

    /**
     * @param int $reviewCount
     * @return OvertimeDto
     */
    public function setReviewCount(int $reviewCount): OvertimeDto
    {
        $this->reviewCount = $reviewCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getAverageScore(): int
    {
        return $this->averageScore;
    }

    /**
     * @param int $averageScore
     * @return OvertimeDto
     */
    public function setAverageScore(int $averageScore): OvertimeDto
    {
        $this->averageScore = $averageScore;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateGroup(): string
    {
        return $this->dateGroup;
    }

    /**
     * @param string $dateGroup
     * @return OvertimeDto
     */
    public function setDateGroup(string $dateGroup): OvertimeDto
    {
        $this->dateGroup = $dateGroup;
        return $this;
    }
}