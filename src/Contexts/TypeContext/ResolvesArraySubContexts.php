<?php

namespace Nuxtifyts\PhpDto\Contexts\TypeContext;

use InvalidArgumentException;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfBackedEnums;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfDateTimes;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use ReflectionProperty;

/**
 * @mixin TypeContext<Type>
 */
trait ResolvesArraySubContexts
{
    /**
     * @return list<TypeContext<Type>>
     */
    protected static function resolveSubContextsForArray(ReflectionProperty $property): array
    {
        $instances = [];

        foreach (self::arrayTypeAttributeClasses() as $attributeClass) {
            foreach ($property->getAttributes($attributeClass) as $reflectionAttribute) {
                $attribute = $reflectionAttribute->newInstance();

                $instances = [
                    ...$instances,
                    ...(match (true) {
                        $attribute instanceof ArrayOfScalarTypes => self::resolveSubContextsForArrayOfScalarTypes($attribute),
                        $attribute instanceof ArrayOfBackedEnums => self::resolveSubContextsForArrayOfBackedEnums($attribute),
                        $attribute instanceof ArrayOfDateTimes => self::resolveSubContextsForArrayOfDateTimes($attribute),
                        $attribute instanceof ArrayOfData => self::resolveSubContextsForArrayOfData($attribute),
                        default => [],
                    })
                ];
            }
        }

        return $instances;
    }

    /**
     * @return list<TypeContext<Type>>
     */
    private static function resolveSubContextsForArrayOfScalarTypes(ArrayOfScalarTypes $attribute): array
    {
        $instances = [];

        foreach ($attribute->types as $type) {
            if (!in_array($type, Type::SCALAR_TYPES, true)) {
                throw new InvalidArgumentException(
                    'Unsupported type passed to ScalarTypeArray: ' . $type->value
                );
            }

            $instances[] = new static($type);
        }

        return $instances;
    }

    /**
     * @return list<TypeContext<Type>>
     */
    private static function resolveSubContextsForArrayOfBackedEnums(ArrayOfBackedEnums $attribute): array
    {
        $instances = [];

        foreach ($attribute->enums as $enum) {
            $reflectionEnum = self::$_enumReflections[$enum]
                ??= $attribute->resolvedBackedEnumReflections[$enum];

            if (!$reflectionEnum->isBacked()) {
                throw new InvalidArgumentException(
                    'Non-backed enum passed to BackedEnumArray: ' . $enum
                );
            }

            $instances[] = new static(Type::BACKED_ENUM, reflection: $reflectionEnum);
        }

        return $instances;
    }

    /** @return list<TypeContext<Type>> */
    private static function resolveSubContextsForArrayOfDateTimes(ArrayOfDateTimes $attribute): array
    {
        $instances = [];

        foreach ($attribute->dateTimes as $dateTime) {
            $reflectionDateTime = self::$_dateTimeReflections[$dateTime]
                ??= $attribute->resolvedDateTimeReflections[$dateTime];

            $instances[] = new static(Type::DATETIME, reflection: $reflectionDateTime);
        }

        return $instances;
    }

    /** @return list<TypeContext<Type>> */
    private static function resolveSubContextsForArrayOfData(ArrayOfData $attribute): array
    {
        $instances = [];

        foreach ($attribute->dataClasses as $dataClass) {
            $reflectionData = self::$_dataReflections[$dataClass]
                ??= $attribute->resolvedDataReflections[$dataClass];

            $instances[] = new static(Type::DATA, reflection: $reflectionData);
        }

        return $instances;
    }

    /**
     * @return list<class-string<object>>
     */
    private static function arrayTypeAttributeClasses(): array
    {
        return [
            ArrayOfData::class,
            ArrayOfDateTimes::class,
            ArrayOfBackedEnums::class,
            ArrayOfScalarTypes::class,
        ];
    }
}
