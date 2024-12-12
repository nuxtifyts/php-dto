<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
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
     * Associated by the class name + :: + property name
     */
    private static array $_instances = [];

    /**
     * @var list<Serializer>|null
     */
    private ?array $_serializers = null;

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

    final public static function getInstance(ReflectionProperty $property): static
    {
        return self::$_instances[self::getKey($property)]
            ??= new static($property);
    }

    private static function getKey(ReflectionProperty $property): string
    {
        return $property->getDeclaringClass()->getName() . '::' . $property->getName();
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
