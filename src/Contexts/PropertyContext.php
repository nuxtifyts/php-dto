<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Nuxtifyts\PhpDto\Attributes\Property\Computed;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
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

    private(set) bool $isComputed = false;

    /**
     * @throws UnsupportedTypeException
     */
    final private function __construct(
        protected readonly ReflectionProperty $_reflectionProperty
    ) {
        $this->syncTypesFromReflectionProperty($this->_reflectionProperty);
        $this->syncPropertyAttributes();
    }

    public string $propertyName {
        get => $this->_reflectionProperty->getName();
    }

    public string $className {
        get => $this->_reflectionProperty->getDeclaringClass()->getName();
    }

    /** @var list<TypeContext<Type>> $arrayTypeContexts */
    public array $arrayTypeContexts {
        get => array_reduce(
            $this->typeContexts ?? [],
            static fn (array $typeContexts, TypeContext $context) => $context->type === Type::ARRAY
                    ? [...$typeContexts, ...$context->subTypeContexts]
                    : [],
            []
        );
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

    private function syncPropertyAttributes(): void
    {
        $this->isComputed = !empty($this->_reflectionProperty->getAttributes(Computed::class));
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

    /**
     * @return list<TypeContext<Type>>
     */
    public function getFilteredSubTypeContexts(Type $type, Type ...$additionalTypes): array
    {
        return array_values(
            array_filter(
                $this->arrayTypeContexts,
                static fn (TypeContext $typeContext) =>
                    in_array($typeContext->type, [$type, ...$additionalTypes], true)
            )
        );
    }

    /**
     * @return list<Serializer>
     *
     * @throws UnknownTypeException
     */
    protected function resolveSerializers(): array
    {
        return $this->getSerializersFromPropertyContext($this);
    }

    /**
     * @param array<string, mixed> $value
     *
     * @throws DeserializeException
     * @throws UnknownTypeException
     */
    public function deserializeFrom(array $value): mixed
    {
        foreach ($this->serializers() as $serializer) {
            try {
                return $serializer->deserialize($this, $value);
            } catch (DeserializeException) {
            }
        }

        throw new DeserializeException('Could not  deserialize value for property: ' . $this->propertyName);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     * @throws UnknownTypeException
     */
    public function serializeFrom(object $object): array
    {
        foreach ($this->serializers() as $serializer) {
            try {
                return $serializer->serialize($this, $object);
            } catch (SerializeException) {
            }
        }

        throw new SerializeException('Could not serialize value for property: ' . $this->propertyName);
    }
}
