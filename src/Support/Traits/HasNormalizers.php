<?php

namespace Nuxtifyts\PhpDto\Support\Traits;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Normalizers\ArrayAccessNormalizer;
use Nuxtifyts\PhpDto\Normalizers\ArrayNormalizer;
use Nuxtifyts\PhpDto\Normalizers\JsonStringNormalizer;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Normalizers\StdClassNormalizer;

trait HasNormalizers
{
    /**
     * @param class-string<Data> $class
     *
     * @return array<string, mixed>|false
     */
    protected static function normalizeValue(mixed $value, string $class): array|false
    {
        foreach (static::allNormalizer() as $normalizer) {
            $normalized = new $normalizer($value, $class)->normalize();

            if ($normalized !== false) {
                return $normalized;
            }
        }

        return false;
    }

    /**
     * @return non-empty-array<int<0, max>, class-string<Normalizer>>
     */
    final protected static function allNormalizer(): array
    {
        return array_values(array_unique([
            ...static::normalizers(),
            JsonStringNormalizer::class,
            StdClassNormalizer::class,
            ArrayAccessNormalizer::class,
            ArrayNormalizer::class,
        ]));
    }

    /**
     * @return list<class-string<Normalizer>>
     */
    protected static function normalizers(): array
    {
        return [];
    }
}
