<?php

namespace Nuxtifyts\PhpDto\Contexts;

use Nuxtifyts\PhpDto\Attributes\Property\Aliases;
use Nuxtifyts\PhpDto\Attributes\Property\CipherTarget;
use Nuxtifyts\PhpDto\Attributes\Property\Computed;
use Nuxtifyts\PhpDto\Attributes\Property\DefaultsTo;
use Nuxtifyts\PhpDto\Attributes\Property\WithRefiner;
use Nuxtifyts\PhpDto\DataCiphers\CipherConfig;
use Nuxtifyts\PhpDto\DataRefiners\DataRefiner;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use Nuxtifyts\PhpDto\FallbackResolver\FallbackConfig;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use Nuxtifyts\PhpDto\Support\Traits\HasTypes;
use ReflectionProperty;
use ReflectionAttribute;
use Exception;

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

    private(set) ?CipherConfig $cipherConfig = null;

    private(set) ?FallbackConfig $fallbackConfig = null;

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

        if ($cipherTargetAttribute = $this->reflection->getAttributes(CipherTarget::class)[0] ?? null) {
            /** @var ReflectionAttribute<CipherTarget> $cipherTargetAttribute */
            $instance = $cipherTargetAttribute->newInstance();

            $this->cipherConfig = new CipherConfig(
                dataCipherClass: $instance->dataCipherClass,
                secret: $instance->secret ?: $this->reflection->getName(),
                encoded: $instance->encoded
            );
        }

        if ($defaultsToAttribute = $this->reflection->getAttributes(DefaultsTo::class)[0] ?? null) {
            /** @var ReflectionAttribute<DefaultsTo> $defaultsToAttribute */
            $instance = $defaultsToAttribute->newInstance();

            $this->fallbackConfig = new FallbackConfig(
                value: $instance->value,
                resolverClass: $instance->fallbackResolverClass
            );
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
                $serializedData = $serializer->serialize($this, $object);
            } catch (SerializeException) {
            }
        }

        if (empty($serializedData)) {
            throw new SerializeException('Could not serialize value for property: ' . $this->propertyName);
        }

        try {
            if ($this->cipherConfig) {
                return array_map(
                    fn (mixed $value) => $this->cipherConfig->dataCipherClass::cipher(
                        data: $value,
                        secret: $this->cipherConfig->secret,
                        encode: $this->cipherConfig->encoded
                    ),
                    $serializedData
                );
            }

            return $serializedData;
        } catch (Exception) {
            throw new SerializeException('Could not serialize value for property: ' . $this->propertyName);
        }
    }
}
