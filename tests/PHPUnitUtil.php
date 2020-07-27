<?php


namespace App\Tests;


class PHPUnitUtil
{
    /**
     * @param $obj
     * @param $name
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    public static function getPrivateMethod($obj, $name) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}