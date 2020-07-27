<?php

namespace App\Tests\Service;

use App\Dto\BenchmarkDto;
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
        $this->reviewRepository
            ->expects($this->any())
            ->method('getAverageScore')
            ->will($this->returnValue($dataset));
        $output = $this->benchmarkService->generate(
            $hotel,
            new \DateTime(),
            new \DateTime(),
        );
        $this->assertEquals($expected, $output, "Expected output to be a BenchmarkDto");
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
            [
                [],
                null,
            ],
            [
                [$dataset[0]],
                new BenchmarkDto(5, 5, null),
            ],
            [
                [$dataset[0], $dataset[1]],
                new BenchmarkDto(5, 7, null),
            ],
            [
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
        $this->assertEquals($score, $output[$id], "Expected value to be $score, $output[$id] returned");
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
            [$quartiles, 4, BenchmarkService::BENCHMARK_TIER_BOTTOM],
            [$quartiles, 14, BenchmarkService::BENCHMARK_TIER_TOP],
            [$quartiles, 5, null],
            [$quartiles, 13, null],
            [$quartiles, 6, null]
        ];
    }
}
