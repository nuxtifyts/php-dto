<?php

namespace Nuxtifyts\PhpDto\Contexts;

use DateTimeInterface;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use ReflectionEnum;
use BackedEnum;
use Exception;
use DateTime;
use DateTimeImmutable;
use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;

/**
 * @template T of Type
 */
class TypeContext
{
    use TypeContext\ResolvesArraySubContexts;
    use HasSerializers {
        serializers as subTypeSerializers;
    }

    /** @var array<string, ReflectionEnum<BackedEnum>> */
    private static array $_enumReflections = [];

    /** @var array<string, ReflectionClass<DateTime|DateTimeImmutable>> */
    private static array $_dateTimeReflections = [];

    /** @var array<string, ReflectionClass<Data>> */
    private static array $_dataReflections = [];

    /** @var list<Type> $arrayElementTypes */
    public array $arrayElementTypes {
        get => array_map(
            static fn (TypeContext $context) => $context->type,
            $this->subTypeContexts ?? []
        );
    }

    /**
     * @param ?ReflectionClass<object> $reflection
     * @param ?list<static<T>> $subTypeContexts
     */
    final protected function __construct(
        public readonly Type $type,
        public readonly ?ReflectionClass $reflection = null,
        public readonly ?array $subTypeContexts = null
    ) {
    }

    /**
     * @return list<static<T>>
     *
     * @throws UnsupportedTypeException
     */
    public static function getInstances(ReflectionProperty $property): array
    {
        $reflectionTypes = self::getPropertyStringTypes($property);
        $instances = [];

        foreach ($reflectionTypes as $type) {
            switch (true) {
                case in_array($type, ['double', 'float']):
                    $instances[] = new static(Type::FLOAT);
                    break;
                case in_array($type, ['int', 'integer']):
                    $instances[] = new static(Type::INT);
                    break;
                case in_array($type, ['bool', 'boolean']):
                    $instances[] = new static(Type::BOOLEAN);
                    break;
                case $type === 'string':
                    $instances[] = new static(Type::STRING);
                    break;
                case $type === 'null':
                    break;
                case ($reflectionEnum = self::resolvesReflectionEnum($type)):
                    $instances[] = new static(
                        Type::BACKED_ENUM,
                        reflection: $reflectionEnum
                    );
                    break;
                case ($reflectionDateTime = self::resolvesDateTime($type)):
                    $instances[] = new static(
                        Type::DATETIME,
                        reflection: $reflectionDateTime
                    );
                    break;
                case ($reflectionData = self::resolvesData($type)):
                    $instances[] = new static(
                        Type::DATA,
                        reflection: $reflectionData
                    );
                    break;
                case $type === 'array':
                    $instances[] = new static(
                        Type::ARRAY,
                        subTypeContexts: self::resolveSubContextsForArray($property)
                    );
                    break;
                default:
                    throw UnsupportedTypeException::from($type);
            }
        }

        return $instances;
    }

    /**
     * @return ?ReflectionEnum<BackedEnum>
     */
    private static function resolvesReflectionEnum(string $type): ?ReflectionEnum
    {
        if (enum_exists($type)) {
            /**
             * @var ReflectionEnum<BackedEnum> $reflection
             * @phpstan-ignore-next-line
             */
            $reflection = self::$_enumReflections[$type] ??= new ReflectionEnum($type);

            if ($reflection->isBacked()) {
                self::$_enumReflections[$type] = $reflection;

                return $reflection;
            }
        }

        return null;
    }

    /**
     * @return ?ReflectionClass<DateTime|DateTimeImmutable>
     */
    private static function resolvesDateTime(string $type): ?ReflectionClass
    {
        try {
            if (class_exists($type) || interface_exists($type)) {
                /**
                 * @var ReflectionClass<DateTime|DateTimeImmutable> $reflection
                 * @phpstan-ignore-next-line
                 */
                $reflection = self::$_dateTimeReflections[$type] ??= new ReflectionClass($type);

                if ($reflection->implementsInterface(DateTimeInterface::class)) {
                    self::$_dateTimeReflections[$type] = $reflection;

                    return $reflection;
                }
            }
            // @codeCoverageIgnoreStart
        } catch (Exception) {
        }
        // @codeCoverageIgnoreEnd

        return null;
    }

    /**
     * @return ?ReflectionClass<Data>
     */
    private static function resolvesData(string $type): ?ReflectionClass
    {
        try {
            if (class_exists($type)) {
                /**
                 * @var ReflectionClass<Data> $reflection
                 * @phpstan-ignore-next-line
                 */
                $reflection = self::$_dataReflections[$type] ??= new ReflectionClass($type);

                if ($reflection->implementsInterface(BaseDataContract::class)) {
                    return $reflection;
                }
            }
        } catch (Exception) {
        }

        return null;
    }

    /**
     * @return list<string>
     *
     * Should be called once for each unique property.
     * If called many times, return should be cached
     */
    private static function getPropertyStringTypes(ReflectionProperty $property): array
    {
        return match(true) {
            ($type = $property->getType()) instanceof ReflectionNamedType => [
                $type->getName(),
            ],
            $type instanceof ReflectionUnionType => array_values(array_map(
                static fn (ReflectionType $type): string => $type instanceof ReflectionNamedType
                    ? $type->getName()
                    : '',
                $type->getTypes()
            )),
            default => [],
        };
    }

    /**
     * @return list<Serializer>
     */
    protected function resolveSerializers(): array
    {
        return $this->getSerializersFromTypeContext($this);
    }
}
