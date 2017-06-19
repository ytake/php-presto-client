<?php
declare(strict_types=1);

/**
 * Trait TestReflectionTrait
 */
trait TestReflectionTrait
{
    /**
     * @param $class
     * @param $name
     *
     * @return \ReflectionProperty
     */
    protected function getProtectProperty($class, $name): \ReflectionProperty
    {
        $class = new \ReflectionClass($class);
        $property = $class->getProperty($name);
        $property->setAccessible(true);

        return $property;
    }
}
