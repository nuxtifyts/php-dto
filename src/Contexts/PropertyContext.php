<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use Nuxtifyts\PhpDto\Support\Traits\HasTypes;
use ReflectionProperty;

class PropertyContext
{
    use HasSerializers;
    use HasTypes;

    /**
     * @var array<string, static>
     *
     * Instances of the PropertyContext class.
     * Associated by the class name + @ + property name
     */
    private static array $_instances = [];

    /**
     * @var list<Serializer>|null
     */
    private ?array $_serializers = null;

    /**
     * @throws UnsupportedTypeException
     */
    final private function __construct(
        protected readonly ReflectionProperty $_reflectionProperty
    ) {
        $this->syncTypesFromReflectionProperty($this->_reflectionProperty);
    }

    public string $propertyName {
        get => $this->_reflectionProperty->getName();
    }

    public string $className {
        get => $this->_reflectionProperty->getDeclaringClass()->getName();
    }

    /**
     * @throws UnsupportedTypeException
     */
    final public static function getInstance(ReflectionProperty $property): static
    {
        return self::$_instances[self::getKey($property)]
            ??= new static($property);
    }

    private static function getKey(ReflectionProperty $property): string
    {
        return $property->getDeclaringClass()->getName() . '@' . $property->getName();
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

    /**
     * @return list<TypeContext<Type>>
     */
    public function getFilteredTypeContexts(Type $type, Type ...$additionalTypes): array
    {
        return array_values(
            array_filter(
                $this->typeContexts,
                static fn (TypeContext $typeContext) =>
                    in_array($typeContext->type, [$type, ...$additionalTypes], true)
            )
        );
    }
}
