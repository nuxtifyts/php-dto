<?php

namespace Nuxtifyts\PhpDto\Support;

use BackedEnum;
use InvalidArgumentException;

final readonly class Arr
{
    /**
     * @param array<array-key, mixed> $array
     * @param array<array-key, mixed> $default
     *
     * @return array<array-key, mixed>
     */
    public static function getArray(array $array, string $key, array $default = []): array
    {
        $value = $array[$key] ?? null;

        return is_array($value) ? $value : $default;
    }

    /** 
     *  @param array<array-key, mixed> $array
     */
    public static function getStringOrNull(array $array, string $key): ?string
    {
        $value = $array[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /** 
     * @param array<array-key, mixed> $array
     */
    public static function getString(array $array, string $key, string $default = ''): string
    {
        return self::getStringOrNull($array, $key) ?? $default;
    }

        /** 
     * @param array<array-key, mixed> $array
     */
    public static function getIntegerOrNull(array $array, string $key): ?int
    {
        $value = $array[$key] ?? null;

        return is_int($value) ? $value : null;
    }

    /** 
     * @param array<array-key, mixed> $array
     */
    public static function getInteger(array $array, string $key, int $default = 0): int
    {
        return self::getIntegerOrNull($array, $key) ?? $default;
    }

    /** 
     * @param array<array-key, mixed> $array
     */
    public static function getFloatOrNull(array $array, string $key): ?float
    {
        $value = $array[$key] ?? null;

        return is_float($value) ? $value : null;
    }

    /** 
     * @param array<array-key, mixed> $array
     */
    public static function getFloat(array $array, string $key, float $default = 0.0): float
    {
        return self::getFloatOrNull($array, $key) ?? $default;
    }

    /** 
     * @param array<array-key, mixed> $array
     */
    public static function getBooleanOrNull(array $array, string $key): ?bool
    {
        $value = $array[$key] ?? null;

        return is_bool($value) ? $value : null;
    }

    /** 
     * @param array<array-key, mixed> $array
     */
    public static function getBoolean(array $array, string $key, bool $default = false): bool
    {
        return self::getBooleanOrNull($array, $key) ?? $default;
    }

    /** 
     * @template T of BackedEnum 
     * 
     * @param array<array-key, mixed> $array
     * @param class-string<T> $enumClass
     * @param ?T $default
     * 
     * @return ?T
     */
    public static function getBackedEnumOrNull(
        array $array, 
        string $key, 
        string $enumClass, 
        ?BackedEnum $default = null
    ): ?BackedEnum {
        $value = $array[$key] ?? null;

        if ($value instanceof $enumClass) {
            return $value;
        } else if (
            (is_string($value) || is_integer($value))
            && $resolvedValue = $enumClass::tryFrom($value)
        ) {
            return $resolvedValue;
        }

        return is_null($default)
            ? null
            : ($default instanceof $enumClass
                ? $default
                : throw new InvalidArgumentException('Default value must be an instance of ' . $enumClass)
            );        
    }

    /**
     * @param array<array-key, mixed> $array
     * @param class-string<object> $classString
     */
    public static function isArrayOfClassStrings(array $array, string $classString): bool
    {
        return array_all(
            $array,
            static fn (mixed $value): bool => is_string($value)
                && is_subclass_of($value, $classString)
        );
    }
}
