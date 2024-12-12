<?php

namespace Nuxtifyts\PhpDto\Contexts;

use ReflectionClass;
use ReflectionException;

/**
 * @template T of object
 */
class ClassContext
{
    /**
     * @var array<string, static>
     *
     * Instances of the ClassContext class.
     * Associated by the class name
     */
    private static array $_instances = [];

    /**
     * @var array<string, PropertyContext>
     */
    protected readonly array $_properties;

    /**
     * @param ReflectionClass<T> $_reflectionClass
     */
    final private function __construct(
        protected readonly ReflectionClass $_reflectionClass
    ) {
        $this->_properties = self::getPropertyContexts($this->_reflectionClass);
    }

    /** @var array<string, PropertyContext> */
    public array $properties {
        get => $this->_properties;
    }

    /**
     * @param ReflectionClass<T> $reflectionClass
     */
    final public static function getInstance(ReflectionClass $reflectionClass): static
    {
        return self::$_instances[self::getKey($reflectionClass)]
            ??= new static($reflectionClass);
    }

    /**
     * @param ReflectionClass<T> $reflectionClass
     */
    private static function getKey(ReflectionClass $reflectionClass): string
    {
        return $reflectionClass->getName();
    }

    /**
     * @param ReflectionClass<T> $reflectionClass
     *
     * @return array<string, PropertyContext>
     */
    private static function getPropertyContexts(ReflectionClass $reflectionClass): array
    {
        $properties = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $properties[$property->getName()] = PropertyContext::getInstance($property);
        }

        return $properties;
    }

    /**
     * @throws ReflectionException
     *
     * @return T
     */
    public function newInstanceWithoutConstructor(): mixed
    {
        return $this->_reflectionClass->newInstanceWithoutConstructor();
    }
}
