<?php

namespace Heterodoks\LaravelSendy\Tests;

trait TestHelper
{
    /**
     * Set protected/private property value
     */
    protected function setProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setValue($object, $value);
    }
} 