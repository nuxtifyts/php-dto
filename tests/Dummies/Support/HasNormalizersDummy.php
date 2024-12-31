<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Support;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Normalizers\Concerns\HasNormalizers;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\DummyNormalizer;

final class HasNormalizersDummy
{
    use HasNormalizers;

    /**
     * @param class-string<Data> $class
     *
     * @return array<string, mixed>|false
     *
     * @throws DataConfigurationException
     */
    public static function testNormalizeValue(mixed $value, string $class): array|false
    {
        return self::normalizeValue($value, $class);
    }

    /**
     * @return list<class-string<Normalizer>>
     *
     * @throws DataConfigurationException
     */
    public static function getAllNormalizer(): array
    {
        return self::allNormalizer();
    }

    /**
     * @return list<class-string<Normalizer>>
     */
    public static function normalizers(): array
    {
        return [
            DummyNormalizer::class
        ];
    }
}
