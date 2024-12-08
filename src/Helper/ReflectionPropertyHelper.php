<?php

namespace Nuxtifyts\PhpDto\Helper;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class ReflectionPropertyHelper
{
    /**
     * @var array<string, list<string>>
     *
     * List of property types associated by property name.
     * Property name is identified by the class name and property name: `ClassName::propertyName`
     */
    private static array $propertyCache = [];

    /**
     * @return list<string>
     */
    public static function getPropertyTypes(ReflectionProperty $property): array
    {
        return self::$propertyCache[$property->getDeclaringClass()->getName() . '::' . $property->getName()]
            ??= match(true) {
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

    public static function isPropertyNullable(ReflectionProperty $property): bool
    {
        return in_array('null', self::getPropertyTypes($property));
    }
}
