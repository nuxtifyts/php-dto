<?php

namespace Nuxtifyts\PhpDto\Normalizers\Concerns;

use Nuxtifyts\PhpDto\Configuration\DataConfiguration;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;

trait HasNormalizers
{
    /**
     * @param class-string<Data> $class
     *
     * @return array<string, mixed>|false
     *
     * @throws DataConfigurationException
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
     * @return list<class-string<Normalizer>>
     *
     * @throws DataConfigurationException
     */
    final protected static function allNormalizer(): array
    {
        return array_values(array_unique([
            ...static::normalizers(),
            ...DataConfiguration::getInstance()->normalizers->baseNormalizers,
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
