<?php


namespace App\Controller;

use App\Entity\Hotel;
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
     * @var Serializer
     */
    private $serializer;

    public function __construct()
    {
        $encoder = new JsonEncoder();
        $dateCallback = function ($innerObject) {
            return $innerObject instanceof \DateTime ? $innerObject->format(\DateTime::ISO8601) : '';
        };
        $context = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function($object) {
                return $object->getName();
            },
            AbstractNormalizer::CALLBACKS => [
                'createdDate' => $dateCallback,
            ],
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $context);
        $this->serializer = new Serializer([$normalizer], [$encoder]);
    }

    /**
     * @Route("/{id}/overtime")
     */
    public function overtimeAction(Hotel $hotel, Request $request): Response
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired('starting_date')
            ->setRequired('ending_date');

        try {
            $resolver->resolve($request->query->all());
        } catch (MissingOptionsException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        $body = $this->serializer->serialize($hotel, 'json');
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