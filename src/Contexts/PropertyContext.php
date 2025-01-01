<?php

namespace Nuxtifyts\PhpDto\Contexts;

use BackedEnum;
use DateTimeInterface;
use Exception;
use Nuxtifyts\PhpDto\Attributes\Property\Aliases;
use Nuxtifyts\PhpDto\Attributes\Property\CipherTarget;
use Nuxtifyts\PhpDto\Attributes\Property\Computed;
use Nuxtifyts\PhpDto\Attributes\Property\DefaultsTo;
use Nuxtifyts\PhpDto\Attributes\Property\Hidden;
use Nuxtifyts\PhpDto\Attributes\Property\WithRefiner;
use Nuxtifyts\PhpDto\Contexts\Concerns\HasTypes;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\DataCiphers\CipherConfig;
use Nuxtifyts\PhpDto\DataRefiners\DataRefiner;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use Nuxtifyts\PhpDto\FallbackResolver\FallbackConfig;
use Nuxtifyts\PhpDto\Serializers\Concerns\HasSerializers;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionEnum;
use ReflectionException;
use ReflectionProperty;
use UnitEnum;

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

    private(set) bool $isHidden = false;

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
        $this->isHidden = !empty($this->reflection->getAttributes(Hidden::class));

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
     * @throws DataConfigurationException
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
     * @throws DataConfigurationException
     */
    public function deserializeFrom(array $value): mixed
    {
        foreach ($this->serializers() as $serializer) {
            try {
                return $serializer->deserialize($this, $value);
            } catch (DeserializeException) {
            }
        }

        throw DeserializeException::generic();
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    public function serializeFrom(object $object): array
    {
        try {
            foreach ($this->serializers() as $serializer) {
                try {
                    $serializedData = $serializer->serialize($this, $object);
                } catch (SerializeException) {
                }
            }

            if (empty($serializedData)) {
                throw new Exception();
            }

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
        } catch (Exception $e) {
            throw SerializeException::generic($e);
        }
    }

    /**
     * @throws UnsupportedTypeException
     * @throws ReflectionException
     * @throws DataCreationException
     */
    public function emptyValue(): mixed
    {
        if ($this->isNullable) {
            return null;
        }

        if (! $typeContext = $this->typeContexts[0] ?? null) {
            throw UnsupportedTypeException::emptyType();
        }

        switch (true) {
            case $typeContext->type === Type::STRING:
                return '';

            case $typeContext->type === Type::INT:
                return 0;

            case $typeContext->type === Type::FLOAT:
                return 0.0;

            case $typeContext->type === Type::BOOLEAN:
                return false;

            case $typeContext->type === Type::ARRAY:
                return [];

            case $typeContext->type === Type::DATA:
                /** @var null|ReflectionClass<Data> $reflection */
                $reflection = $typeContext->reflection;

                return !$reflection
                    ? throw UnsupportedTypeException::invalidReflection()
                    : ClassContext::getInstance($reflection)->emptyValue();

            case $typeContext->type === Type::BACKED_ENUM:
                /** @var null|ReflectionEnum<UnitEnum|BackedEnum> $reflection */
                $reflection = $typeContext->reflection;

                return $reflection instanceof ReflectionEnum && $reflection->isBacked()
                    ? $reflection->getCases()[0]->getValue()
                    : throw UnsupportedTypeException::invalidReflection();

            default:
                /** @var null|DateTimeInterface $dateTime */
                $dateTime = $typeContext->reflection?->newInstance();

                return $dateTime instanceof DateTimeInterface
                    ? $dateTime
                    : throw UnsupportedTypeException::invalidReflection();
        }
    }
}
