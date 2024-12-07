<?php

namespace Nuxtifyts\PhpDto\Helper;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class ReflectionPropertyHelper
{
    /**
     * @return list<string>
     */
    public static function getPropertyTypes(ReflectionProperty $property): array
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
}
