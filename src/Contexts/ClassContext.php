<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

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
    protected(set) readonly array $properties;

    /** @var list<string> List of param names  */
    public readonly array $constructorParams;

    /**
     * @param ReflectionClass<T> $reflection
     *
     * @throws UnsupportedTypeException
     */
    final private function __construct(
        protected readonly ReflectionClass $reflection
    ) {
        $this->properties = self::getPropertyContexts($this->reflection);
        $this->constructorParams = array_map(
            static fn (ReflectionParameter $param) => $param->getName(),
            $this->reflection->getConstructor()?->getParameters() ?? [],
        );
    }

    public bool $hasComputedProperties {
        get => count(
            array_filter(
                $this->properties,
                static fn (PropertyContext $property) => $property->isComputed
            )
        ) > 0;
    }

    /**
     * @param ReflectionClass<T> $reflectionClass
     *
     * @throws UnsupportedTypeException
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
     *
     * @throws UnsupportedTypeException
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
        return $this->reflection->newInstanceWithoutConstructor();
    }

    /**
     * @throws ReflectionException
     */
    public function newInstanceWithConstructorCall(mixed ...$args): mixed
    {
        return $this->reflection->newInstance(...$args);
    }
}
