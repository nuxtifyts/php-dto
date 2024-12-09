<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class PropertyContext
{
    use HasSerializers;

    /**
     * @var array<string, static>
     *
     * Instances of the PropertyContext class.
     * Associated by the class name + :: + property name
     */
    private static array $_instances = [];

    /**
     * @var list<Serializer>|null
     */
    private ?array $_serializers = null;

    /** @var list<Type> */
    protected(set) readonly array $types;

    protected bool $allowsNull;

    final private function __construct(
        protected readonly ReflectionProperty $_reflectionProperty
    ) {
        $types = self::getPropertyStringTypes($this->_reflectionProperty);

        $this->types = Type::fromReflectionProperty($types);
        $this->allowsNull = in_array('null', $types);
    }

    public string $propertyName {
        get => $this->_reflectionProperty->getName();
    }

    public string $className {
        get => $this->_reflectionProperty->getDeclaringClass()->getName();
    }

    public bool $isNullable {
        get => $this->allowsNull;
    }

    final public static function getInstance(ReflectionProperty $property): static
    {
        return self::$_instances[self::getKey($property)]
            ?? new static($property);
    }

    private static function getKey(ReflectionProperty $property): string
    {
        return $property->getDeclaringClass()->getName() . '::' . $property->getName();
    }

    /**
     * @return list<string>
     *
     * Should be called once for each unique property (class::property)
     * If called many times, return should be cached
     */
    private static function getPropertyStringTypes(ReflectionProperty $property): array
    {
        return match(true) {
            ($type = $property->getType()) instanceof ReflectionNamedType => array_values(
                array_filter([
                    $type->getName(),
                    $type->allowsNull() ? 'null' : null
                ])
            ),
            $type instanceof ReflectionUnionType => array_values(array_filter([
                ...array_map(
                    static fn(ReflectionType $type): string => $type instanceof ReflectionNamedType
                        ? $type->getName()
                        : '',
                    $type->getTypes()
                ),
                $type->allowsNull() ? 'null' : null
            ])),
            default => [],
        };
    }

    /**
     * @return list<Serializer>
     *
     * @throws UnknownTypeException
     */
    public function serializers(): array
    {
        return $this->_serializers ??= $this->getSerializers($this);
    }

    public function getValue(object $object): mixed
    {
        return $this->_reflectionProperty->getValue($object);
    }
}
