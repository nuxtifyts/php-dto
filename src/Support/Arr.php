<?php

namespace Nuxtifyts\PhpDto\Support;

final readonly class Arr
{
    /**
     * @param array<array-key, mixed> $array
     * @param string $key
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
