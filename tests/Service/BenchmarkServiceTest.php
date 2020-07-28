<?php

namespace App\Tests\Service;

use App\Dto\Output\BenchmarkDto;
use App\Dto\Input\BenchmarkParamsDto;
use App\Dto\Input\DateRangeDto;
use App\Service\BenchmarkService;
use App\Tests\PHPUnitUtil;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BenchmarkServiceTest extends TestCase
{
    /**
     * @var BenchmarkService
     */
    private ?BenchmarkService $benchmarkService;

    /**
     * @var MockObject
     */
    private ?MockObject $reviewRepository;

    public function setUp()
    {
        $this->reviewRepository = $this->getMockBuilder('App\Repository\ReviewRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->benchmarkService = new BenchmarkService($this->reviewRepository);
    }

    public function tearDown()
    {
        $this->reviewRepository = null;
        $this->benchmarkService = null;
    }

    /**
     * @dataProvider generateProvider
     */
    public function testGenerate($dataset, $expected)
    {
        $hotel = $this->getMockBuilder('\App\Entity\Hotel')
            ->disableOriginalConstructor()
            ->getMock();
        $hotel
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $dateRange = new DateRangeDto(
            new \DateTime('yesterday'),
            new \DateTime('now'),
        );
        $paramsDto = new BenchmarkParamsDto($hotel, $dateRange);
        $this->reviewRepository
            ->expects($this->any())
            ->method('getAverageScorePerHotel')
            ->will($this->returnValue($dataset));
        $output = $this->benchmarkService->generate($paramsDto);
        $this->assertEquals($expected, $output);
    }

    public function generateProvider()
    {
        $dataset = [
            [
                'hotel_id' => 1,
                'average_score' => 5,
            ],
            [
                'hotel_id' => 2,
                'average_score' => 9,
            ],
            [
                'hotel_id' => 3,
                'average_score' => 13,
            ],
        ];

        return [
            'empty dataset' => [
                [],
                null,
            ],
            'one record dataset' => [
                [$dataset[0]],
                new BenchmarkDto(5, 5, null),
            ],
            'two records dataset' => [
                [$dataset[0], $dataset[1]],
                new BenchmarkDto(5, 7, null),
            ],
            'larger dataset' => [
                [$dataset[0], $dataset[1], $dataset[2]],
                new BenchmarkDto(5,9, null),
            ]
        ];
    }

    public function testNormalizeResult()
    {
        $id = 1;
        $score = 100;
        $input = [
            [
                'hotel_id' => $id,
                'average_score' => $score,
            ]
        ];
        $normalizeResult = PHPUnitUtil::getPrivateMethod($this->benchmarkService, 'normalizeResult');
        $output = $normalizeResult->invoke($this->benchmarkService, $input);
        $this->assertArrayHasKey($id, $output, "Expected output to have key $id");
        $this->assertEquals($score, $output[$id]);
    }

    /**
     * @dataProvider tierProvider
     * @param $quartiles
     * @param $number
     * @param $expected
     * @throws \ReflectionException
     */
    public function testGetTier($quartiles, $number, $expected)
    {
        $getTier = PHPUnitUtil::getPrivateMethod($this->benchmarkService, 'getTier');
        $output = $getTier->invokeArgs($this->benchmarkService, [$quartiles, $number]);
        $this->assertEquals($expected, $output, "Expected result to be $expected");
    }

    public function tierProvider()
    {
        $quartiles = [
            'first' => 5,
            'second' => 9,
            'third' => 13,
        ];

        return [
            'bottom tier' => [$quartiles, 4, BenchmarkService::BENCHMARK_TIER_BOTTOM],
            'top tier' => [$quartiles, 14, BenchmarkService::BENCHMARK_TIER_TOP],
            'bottom edge tier' => [$quartiles, 5, null],
            'top edge tier' => [$quartiles, 13, null],
            'middle tier' => [$quartiles, 6, null]
        ];
    }
}
