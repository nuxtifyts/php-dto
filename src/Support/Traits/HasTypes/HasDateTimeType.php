<?php

namespace Nuxtifyts\PhpDto\Support\Traits\HasTypes;

use ReflectionClass;
use DateTimeInterface;
use Exception;

trait HasDateTimeType
{
    /** @var array<string, ReflectionClass<DateTimeInterface>> */
    private static array $_dateTimeReflections = [];

    /** @var array<string, ReflectionClass<DateTimeInterface>> */
    public array $dateTimeReflections {
        get => self::$_dateTimeReflections;
    }

    private static function isDateTime(string $type): bool
    {
        try {
            if (class_exists($type) || interface_exists($type)) {
                /** @var ReflectionClass<DateTimeInterface> $reflection */
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
