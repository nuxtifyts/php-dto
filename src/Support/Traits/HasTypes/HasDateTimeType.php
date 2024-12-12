<?php

namespace Nuxtifyts\PhpDto\Support\Traits\HasTypes;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Support\Traits\HasTypes;
use ReflectionClass;
use DateTimeInterface;
use Exception;

/**
 * @mixin HasTypes
 * @mixin PropertyContext
 */
trait HasDateTimeType
{
    /** @var array<string, ReflectionClass<object>> */
    private static array $_dateTimeReflections = [];

    /** @var array<string, ReflectionClass<object>> */
    public array $dateTimeReflections {
        get => self::$_dateTimeReflections;
    }

    private static function isDateTimeType(string $type): bool
    {
        try {
            if (class_exists($type) || interface_exists($type)) {
                /** @var ReflectionClass<object> $reflection */
                $reflection = self::$_dateTimeReflections[$type] ??= new ReflectionClass($type);

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
