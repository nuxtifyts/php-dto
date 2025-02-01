<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Exception;
use Nuxtifyts\PhpDto\Attributes\Class\Lazy;
use Nuxtifyts\PhpDto\Attributes\Class\MapName;
use Nuxtifyts\PhpDto\Attributes\Class\WithNormalizer;
use Nuxtifyts\PhpDto\Contexts\ClassContext\NameMapperConfig;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use ReflectionAttribute;
use ReflectionException;
use ReflectionParameter;
use ReflectionClass;

/**
 * @template T of Data
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

    /** @var array<array-key, class-string<Normalizer>> */
    private(set) array $normalizers = [];

    private(set) ?NameMapperConfig $nameMapperConfig = null;

    private(set) bool $isLazy = false;

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
        $this->syncClassAttributes();
    }

    public bool $hasComputedProperties {
        get {
            return count(
                array_filter(
                    $this->properties,
                    static fn (PropertyContext $property) => $property->isComputed
                )
            ) > 0;
        }
    }

    /**
     * @param ReflectionClass<T>|class-string<T> $reflectionClass
     *
     * @throws UnsupportedTypeException
     * @throws ReflectionException
     */
    final public static function getInstance(string|ReflectionClass $reflectionClass): static
    {
        $instance = self::$_instances[self::getKey($reflectionClass)] ?? null;

        if ($instance) {
            return $instance;
        }

        if (is_string($reflectionClass)) {
            $reflectionClass = new ReflectionClass($reflectionClass);
        }

        return self::$_instances[self::getKey($reflectionClass)]
            = new static($reflectionClass);
    }

    /**
     * @param ReflectionClass<T>|class-string<T> $reflectionClass
     */
    private static function getKey(string|ReflectionClass $reflectionClass): string
    {
        return is_string($reflectionClass) ? $reflectionClass : $reflectionClass->getName();
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

    private function syncClassAttributes(): void
    {
        foreach ($this->reflection->getAttributes(WithNormalizer::class) as $withNormalizerAttribute) {
            /** @var ReflectionAttribute<WithNormalizer> $withNormalizerAttribute */
            $this->normalizers = array_values([
                ...$this->normalizers,
                ...$withNormalizerAttribute->newInstance()->classStrings
            ]);
        }

        if ($nameMapperAttribute = $this->reflection->getAttributes(MapName::class)[0] ?? null) {
            /** @var ReflectionAttribute<MapName> $nameMapperAttribute */
            $instance = $nameMapperAttribute->newInstance();

            $this->nameMapperConfig = new NameMapperConfig(
                from: $instance->from,
                to: $instance->to
            );
        }

        $this->isLazy = !empty($this->reflection->getAttributes(Lazy::class));
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
     *
     * @return T
     */
    public function newInstanceWithConstructorCall(mixed ...$args): mixed
    {
        return $this->reflection->newInstance(...$args);
    }

    /**
     * @desc Creates an instance from an array of values using the constructor
     *
     * @param array<string, mixed> $value
     *
     * @return T
     *
     * @throws Exception
     */
    public function constructFromArray(array $value): mixed
    {
        /** @var array<string, mixed> $args */
        $args = [];

        foreach ($this->constructorParams as $paramName) {
            $propertyContext = $this->properties[$paramName] ?? null;

            if (!$propertyContext) {
                throw new Exception('invalid_params_passed');
            }

            $args[$paramName] = $propertyContext->deserializeFrom($value);
        }

        return $this->newInstanceWithConstructorCall(...$args);
    }

    /**
     * @param callable(T $object): T $lazyProxyCallable
     *
     * @return T
     */
    public function newLazyProxy(callable $lazyProxyCallable): mixed
    {
        /** @phpstan-ignore-next-line  */
        return $this->reflection->newLazyProxy($lazyProxyCallable);
    }

    /**
     * @return T
     *
     * @throws ReflectionException
     * @throws UnsupportedTypeException
     * @throws DataCreationException
     */
    public function emptyValue(): mixed
    {
        $emptyValueCreationClosure = function () {
            /** @var array<string, mixed> $args */
            $args = [];

            foreach ($this->constructorParams as $paramName) {
                $propertyContext = $this->properties[$paramName] ?? null;

                if (!$propertyContext) {
                    throw DataCreationException::invalidProperty();
                }

                $args[$paramName] = $propertyContext->emptyValue();
            }

            return $this->newInstanceWithConstructorCall(...$args);
        };

        return $this->isLazy
            ? $this->newLazyProxy($emptyValueCreationClosure)
            : $emptyValueCreationClosure();
    }
}
