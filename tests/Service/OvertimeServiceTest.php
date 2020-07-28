<?php


namespace App\Tests\Service;


use App\Dto\OvertimeDto;
use App\Repository\ReviewRepository;
use App\Service\OvertimeService;
use App\Tests\PHPUnitUtil;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OvertimeServiceTest extends TestCase
{
    /**
     * @var OvertimeService
     */
    private ?OvertimeService $overtimeService;

    /**
     * @var MockObject
     */
    private ?MockObject $reviewRepository;

    public function setUp()
    {
        $this->reviewRepository = $this->getMockBuilder('App\Repository\ReviewRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->overtimeService = new OvertimeService($this->reviewRepository);
    }

    public function tearDown()
    {
        $this->overtimeService = null;
        $this->reviewRepository = null;
    }

    /**
     * @dataProvider getByHotelProvider
     * @param $dataset
     * @param $expected
     * @throws \Exception
     */
    public function testGetByHotel($dataset, $expected)
    {
        $hotel = $this->getMockBuilder('\App\Entity\Hotel')
            ->disableOriginalConstructor()
            ->getMock();
        $this->reviewRepository
            ->expects($this->any())
            ->method('getAverageScoreByDateRange')
            ->will($this->returnValue($dataset));
        $output = $this->overtimeService->getByHotel(
            $hotel,
            new \DateTime('yesterday'),
            new \DateTime('now'),
        );
        $this->assertEquals($expected, $output, "Expected OvertimeDto array to match");
    }

    public function getByHotelProvider()
    {
        $reviewCount = 10;
        $averageScore = 100;

        return [
            [
              [],
              [],
            ],
            [
                [
                    [
                        'count' => $reviewCount,
                        'average' => $averageScore,
                        'group' => '2020-01',
                    ],
                ],
                [new OvertimeDto($reviewCount, $averageScore, '2020-01')],
            ],
            [
                [
                    [
                        'count' => $reviewCount,
                        'average' => $averageScore,
                        'group' => '2020-CW18',
                    ],
                ],
                [new OvertimeDto($reviewCount, $averageScore, '2020-CW18')],
            ],
            [
                [
                    [
                        'count' => $reviewCount,
                        'average' => $averageScore,
                        'group' => '2020-06-03',
                    ],
                ],
                [new OvertimeDto($reviewCount, $averageScore, '2020-06-03')],
            ],
        ];
    }

    /**
     * @dataProvider getGroupingProvider
     * @param $staringDate
     * @param $endingDate
     * @param $expected
     * @throws \ReflectionException
     */
    public function testGetGrouping($staringDate, $endingDate, $expected)
    {
        $getGrouping = PHPUnitUtil::getPrivateMethod($this->overtimeService, 'getGrouping');
        $output = $getGrouping->invoke($this->overtimeService, $staringDate, $endingDate);
        $this->assertEquals($expected, $output, "Expected output to be $output, $expected returned");
    }

    public function getGroupingProvider()
    {
        return [
            [
                new \DateTime('now'),
                new \DateTime(OvertimeService::GROUP_DAILY_LIMIT . ' days ago'),
                ReviewRepository::GROUP_DAILY,
            ],
            [
                new \DateTime('now'),
                new \DateTime((OvertimeService::GROUP_DAILY_LIMIT - 1) . ' days ago'),
                ReviewRepository::GROUP_DAILY,
            ],
            [
                new \DateTime('now'),
                new \DateTime(OvertimeService::GROUP_WEEKLY_LIMIT . ' days ago'),
                ReviewRepository::GROUP_WEEKLY,
            ],
            [
                new \DateTime('now'),
                new \DateTime((OvertimeService::GROUP_WEEKLY_LIMIT - 1) . ' days ago'),
                ReviewRepository::GROUP_WEEKLY,
            ],
            [
                new \DateTime('now'),
                new \DateTime((OvertimeService::GROUP_WEEKLY_LIMIT + 1) . ' days ago'),
                ReviewRepository::GROUP_MONTHLY,
            ],
        ];
    }
}