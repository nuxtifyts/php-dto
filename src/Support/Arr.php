<?php

namespace Nuxtifyts\PhpDto\Support;

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
