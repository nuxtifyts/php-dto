<?php

namespace Nuxtifyts\PhpDto\Normalizers\Concerns;

use Nuxtifyts\PhpDto\Configuration\DataConfiguration;
use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use ReflectionClass;

trait HasNormalizers
{
    /**
     * @param class-string<Data> $class
     * @param array<array-key, class-string<Normalizer>> $classNormalizers
     *
     * @return array<string, mixed>|false
     *
     * @throws DataConfigurationException
     */
    protected static function normalizeValue(
        mixed $value,
        string $class,
        array $classNormalizers = []
    ): array|false {
        foreach (static::allNormalizer($classNormalizers) as $normalizer) {
            $normalized = new $normalizer($value, $class)->normalize();

            if ($normalized !== false) {
                return $normalized;
            }
        }

        return false;
    }

    /**
     * @param array<array-key, class-string<Normalizer>> $classNormalizers
     *
     * @return list<class-string<Normalizer>>
     *
     * @throws DataConfigurationException
     */
    final protected static function allNormalizer(array $classNormalizers = []): array
    {
        return array_values(array_unique([
            ...$classNormalizers,
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
