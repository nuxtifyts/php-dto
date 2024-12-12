<?php

namespace Nuxtifyts\PhpDto\Support\Traits\HasTypes;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Support\Traits\HasTypes;
use ReflectionClass;
use Exception;

/**
 * @mixin HasTypes
 * @mixin PropertyContext
 */
trait HasDataType
{
    /** @var array<string, ReflectionClass<object>> */
    private static array $_dataReflections = [];

    /** @var array<string, ReflectionClass<object>> */
    public array $dataReflections {
        get => self::$_dataReflections;
    }

    private static function isDataType(string $type): bool
    {
        try {
            if (class_exists($type)) {
                /** @var ReflectionClass<object> $reflection */
                $reflection = self::$_dataReflections[$type] ??= new ReflectionClass($type);

                if ($reflection->implementsInterface(BaseDataContract::class)) {
                    return true;
                }
            }
        } catch (Exception) {}

        return false;
    }
}
