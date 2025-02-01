<?php

namespace Nuxtifyts\PhpDto\Configuration;

use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Support\Arr;

class DataConfiguration implements Configuration
{
    protected static ?self $instance = null;

    protected function __construct(
        protected(set) NormalizersConfiguration $normalizers,
        protected(set) SerializersConfiguration $serializers,
    ) {
    }

    /**
     * @param array<array-key, mixed> $config
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

        return self::$instance = new self(
            normalizers: NormalizersConfiguration::getInstance(
                Arr::getArray($config ?? [], 'normalizers'),
                $forceCreate
            ),
            serializers: SerializersConfiguration::getInstance(
                Arr::getArray($config ?? [], 'serializers'),
                $forceCreate
            ),
        );
    }
}
