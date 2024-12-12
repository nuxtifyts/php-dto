<?php

namespace Nuxtifyts\PhpDto\Support\Traits;

use Nuxtifyts\PhpDto\Enums\Property\Type;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use ReflectionClass;
use DateTimeInterface;
use BackedEnum;
use Exception;

trait HasTypes
{
    /** @var array<string, ReflectionEnum<BackedEnum>> */
    private static array $_enumReflections = [];

    /** @var array<string, ReflectionClass<DateTimeInterface>> */
    private static array $_dateTimeReflections = [];

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

    /** @var array<string, ReflectionEnum<BackedEnum>> */
    public array $enumReflections {
        get => self::$_enumReflections;
    }

    /** @var array<string, ReflectionClass<DateTimeInterface>> */
    public array $dateTimeReflections {
        get => self::$_dateTimeReflections;
    }

    /**
     * @return list<string>
     *
     * Should be called once for each unique property (class::property)
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

    private static function isBackedEnum(string $type): bool
    {
        if (enum_exists($type)) {
            $reflection = self::$_enumReflections[$type] ?? new ReflectionEnum($type);

            if ($reflection->isBacked()) {
                self::$_enumReflections[$type] = $reflection;

                return true;
            }
        }

        return false;
    }

    private static function isDateTime(string $type): bool
    {
        try {
            if (class_exists($type) || interface_exists($type)) {
                $reflection = self::$_dateTimeReflections[$type] ?? new ReflectionClass($type);

                if ($reflection->implementsInterface(DateTimeInterface::class)) {
                    self::$_dateTimeReflections[$type] = $reflection;

                    return true;
                }
            }
            // @codeCoverageIgnoreStart
        } catch (Exception) {}
        // @codeCoverageIgnoreEnd

        return false;
    }
}
