<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}