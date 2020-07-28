<?php


namespace App\Controller;

use App\Entity\Hotel;
use App\Service\BenchmarkService;
use App\Service\OvertimeService;
use App\Traits\ApiTrait;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class HotelsController
 * @package App\Controller
 * @Route("api/hotels")
 */
class HotelsController
{
    use ApiTrait;

    /**
     * @Route("/{id}/reviews/overtime")
     * @param Hotel $hotel
     * @param Request $request
     * @param OvertimeService $overtime
     * @return Response
     * @throws \Exception
     */
    public function overtimeAction(Hotel $hotel, Request $request, OvertimeService $overtime): Response
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired('starting_date')
            ->setRequired('ending_date');

        try {
            $query = $resolver->resolve($request->query->all());
        } catch (MissingOptionsException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        $result = $overtime->getByHotel(
            $hotel,
            new \DateTime($query['starting_date']),
            new \DateTime($query['ending_date']),
        );

        return JsonResponse::fromJsonString($this->serialize($result));
    }

    /**
     * @Route("/{id}/benchmark")
     * @param Hotel $hotel
     * @param Request $request
     * @param BenchmarkService $benchmarkService
     * @return Response
     * @throws \Exception
     */
    public function benchmarkAction(Hotel $hotel, Request $request, BenchmarkService $benchmarkService): Response
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired('starting_date')
            ->setRequired('ending_date');

        try {
            $query = $resolver->resolve($request->query->all());
        } catch (MissingOptionsException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        $benchmark = $benchmarkService->generate(
            $hotel,
            new \DateTime($query['starting_date']),
            new \DateTime($query['ending_date']),
        );

        return JsonResponse::fromJsonString($this->serialize($benchmark));
    }
}