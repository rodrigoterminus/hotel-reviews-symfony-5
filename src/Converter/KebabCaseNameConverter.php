<?php


namespace App\Converter;


use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use function Symfony\Component\String\u;

class KebabCaseNameConverter implements NameConverterInterface
{

    /**
     * Converts a property name to its normalized value.
     *
     * @param string $propertyName
     * @return string
     */
    public function normalize(string $propertyName)
    {
        return str_replace('_', '-', u($propertyName)->snake());
    }

    /**
     * Converts a property name to its denormalized value.
     *
     * @param string $propertyName
     * @return string
     */
    public function denormalize(string $propertyName)
    {
        return u($propertyName)->camel();
    }
}