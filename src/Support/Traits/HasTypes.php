<?php

namespace Nuxtifyts\PhpDto\Support\Traits;

use Nuxtifyts\PhpDto\Enums\Property\Type;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

trait HasTypes
{
    use HasTypes\HasEnumType;
    use HasTypes\HasDateTimeType;

    /** @var list<Type> */
    protected(set) array $_types = [];

    protected bool $_allowsNull = false;

    public bool $isNullable {
        get => $this->_allowsNull;
    }

    /** @var list<Type> */
    public array $types {
        get => $this->_types;
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

    protected function syncTypesFromReflectionProperty(ReflectionProperty $property): void
    {
        $reflectionTypes = self::getPropertyStringTypes($property);
        $types = [];

        foreach ($reflectionTypes as $type) {
            switch(true) {
                case in_array($type, ['double', 'float']):
                    $types[] = Type::FLOAT;
                    break;
                case in_array($type, ['int', 'integer']):
                    $types[] = Type::INT;
                    break;
                case in_array($type, ['bool', 'boolean']):
                    $types[] = Type::BOOLEAN;
                    break;
                case $type === 'string':
                    $types[] = Type::STRING;
                    break;
                case $type === 'null':
                    $this->_allowsNull = true;
                    break;
                case self::isBackedEnum($type):
                    $types[] = Type::BACKED_ENUM;
                    break;
                case self::isDateTime($type):
                    $types[] = Type::DATETIME;
                    break;
                default:
                    $types[] = Type::MIXED;
            }
        }

        $this->_types = $types;
    }
}
