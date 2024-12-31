<?php

namespace Nuxtifyts\PhpDto\Configuration;

use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Normalizers\ArrayAccessNormalizer;
use Nuxtifyts\PhpDto\Normalizers\ArrayNormalizer;
use Nuxtifyts\PhpDto\Normalizers\JsonStringNormalizer;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Normalizers\StdClassNormalizer;
use Nuxtifyts\PhpDto\Support\Arr;

class NormalizersConfiguration implements Configuration
{
    protected static ?self $instance = null;

    /**
     * @param array<array-key, class-string<Normalizer>> $baseNormalizers
     */
    protected function __construct(
        protected(set) array $baseNormalizers = [
            JsonStringNormalizer::class,
            StdClassNormalizer::class,
            ArrayAccessNormalizer::class,
            ArrayNormalizer::class,
        ]
    ) {
    }

    /**
     * @param ?array<array-key, mixed> $config
     *
     * @throws DataConfigurationException
     */
    public static function getInstance(
        ?array $config = null,
        bool $forceCreate = false
    ): self {
        if (self::$instance && !$forceCreate) {
            return self::$instance;
        }

        $baseNormalizers = $config['baseNormalizers'] ?? [
            JsonStringNormalizer::class,
            StdClassNormalizer::class,
            ArrayAccessNormalizer::class,
            ArrayNormalizer::class,
        ];

        if (
            !is_array($baseNormalizers)
            || !Arr::isArrayOfClassStrings($baseNormalizers, Normalizer::class)
        ) {
            throw DataConfigurationException::invalidBaseNormalizers();
        }
        /** @var array<array-key, class-string<Normalizer>> $baseNormalizers */

        return self::$instance = new self(
            baseNormalizers: $baseNormalizers
        );
    }
}
