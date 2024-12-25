<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Nuxtifyts\PhpDto\Attributes\Property\Aliases;
use Nuxtifyts\PhpDto\Attributes\Property\Computed;
use Nuxtifyts\PhpDto\Attributes\Property\WithRefiner;
use Nuxtifyts\PhpDto\DataRefiners\DataRefiner;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use Nuxtifyts\PhpDto\Support\Traits\HasTypes;
use ReflectionProperty;
use ReflectionAttribute;

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
     * @var list<string>
     */
    private(set) array $aliases = [];

    private(set) bool $isComputed = false;

    /** @var list<DataRefiner> */
    private(set) array $dataRefiners = [];

    /**
     * @throws UnsupportedTypeException
     */
    final private function __construct(
        protected(set) readonly ReflectionProperty $reflection
    ) {
        $this->syncTypesFromReflectionProperty($this->reflection);
        $this->syncPropertyAttributes();
    }

    public string $propertyName {
        get => $this->reflection->getName();
    }

    public string $className {
        get => $this->reflection->getDeclaringClass()->getName();
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
        $this->isComputed = !empty($this->reflection->getAttributes(Computed::class));

        foreach ($this->reflection->getAttributes(WithRefiner::class) as $withRefinerAttribute) {
            /** @var ReflectionAttribute<WithRefiner> $withRefinerAttribute */
            $this->dataRefiners[] = $withRefinerAttribute->newInstance()->getRefiner();
        }

        if ($aliasesAttribute = $this->reflection->getAttributes(Aliases::class)[0] ?? null) {
            /** @var ReflectionAttribute<Aliases> $aliasesAttribute */
            $this->aliases = $aliasesAttribute->newInstance()->aliases;
        }
    }

    public function getValue(object $object): mixed
    {
        return $this->reflection->getValue($object);
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
