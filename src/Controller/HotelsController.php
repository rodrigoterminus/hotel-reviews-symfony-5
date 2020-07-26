<?php


namespace App\Controller;

use App\Entity\Hotel;
use App\Service\OvertimeService;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class HotelsController
 * @package App\Controller
 * @Route("api/hotels")
 */
class HotelsController
{
    /**
     * @Route("/{id}/overtime")
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

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $body = $serializer->serialize($result, 'json');
        return JsonResponse::fromJsonString($body);
    }

    /**
     * @Route("{id}/benchmark")
     */
    public function benchmarkAction(): Response
    {
        return new Response(null, Response::HTTP_NOT_IMPLEMENTED);
    }
}