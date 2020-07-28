<?php


namespace App\Traits;


use App\Converter\KebabCaseNameConverter;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait ApiTrait
{
    protected function validateQueryParams(array $params, array $criteria)
    {

    }

    /**
     * Serialize Input to be sent as response body
     *
     * @param $input
     * @param int|null $status
     * @param array|null $headers
     * @return string JSON string
     */
    protected function serialize($input, int $status = null, array $headers = null): string
    {
        $kebabCaseNameConverter = new KebabCaseNameConverter();
        $normalizer = new ObjectNormalizer(null, $kebabCaseNameConverter);
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);
        return $serializer->serialize($input, 'json');
    }
}