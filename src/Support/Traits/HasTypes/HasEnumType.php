<?php

namespace Nuxtifyts\PhpDto\Support\Traits\HasTypes;

use ReflectionEnum;
use BackedEnum;

trait HasEnumType
{
    /** @var array<string, ReflectionEnum<BackedEnum>> */
    private static array $_enumReflections = [];

    /** @var array<string, ReflectionEnum<BackedEnum>> */
    public array $enumReflections {
        get => self::$_enumReflections;
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
}
