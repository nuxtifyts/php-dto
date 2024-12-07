<?php

namespace Nuxtifyts\PhpDto\Support\Data;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Serializers\Serializer;

class DataCacheHelper
{
    /**
     * @var array<class-string<Data>, array<string, Serializer>>
     *
     * Array of cached serializers for each class
     */
    private static array $cache = [];

    /**
     * @param class-string<Data> $key
     *
     * @return array<string, Serializer>
     */
    public static function get(string $key): array
    {
        return self::$cache[$key] ??= [];
    }

    /**
     * @param class-string<Data> $key
     *
     * @param array<string, Serializer> $value
     */
    public static function append(string $key, array $value): void
    {
        self::$cache[$key] = [...self::get($key), ...$value];
    }
}
