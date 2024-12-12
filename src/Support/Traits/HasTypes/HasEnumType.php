<?php

namespace Nuxtifyts\PhpDto\Support\Traits\HasTypes;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Support\Traits\HasTypes;
use ReflectionEnum;
use UnitEnum;

/**
 * @mixin HasTypes
 * @mixin PropertyContext
 */
trait HasEnumType
{
    /** @var array<string, ReflectionEnum<UnitEnum>> */
    private static array $_enumReflections = [];

    /** @var array<string, ReflectionEnum<UnitEnum>> */
    public array $enumReflections {
        get => self::$_enumReflections;
    }

    private static function isBackedEnumType(string $type): bool
    {
        if (enum_exists($type)) {
            $reflection = self::$_enumReflections[$type] ??= new ReflectionEnum($type);

            if ($reflection->isBacked()) {
                self::$_enumReflections[$type] = $reflection;

                return true;
            }
        }

        return false;
    }
}
