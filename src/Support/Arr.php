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
}
