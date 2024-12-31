<?php

namespace Nuxtifyts\PhpDto\Configuration;

use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Serializers\ArraySerializer;
use Nuxtifyts\PhpDto\Serializers\BackedEnumSerializer;
use Nuxtifyts\PhpDto\Serializers\DataSerializer;
use Nuxtifyts\PhpDto\Serializers\DateTimeSerializer;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;

class SerializersConfiguration implements Configuration
{
    protected static ?self $instance = null;

    /**
     * @param array<array-key, class-string<Serializer>> $baseSerializers
     */
    protected function __construct(
        protected(set) array $baseSerializers = [
            ArraySerializer::class,
            DataSerializer::class,
            DateTimeSerializer::class,
            BackedEnumSerializer::class,
            ScalarTypeSerializer::class,
        ],
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

        $baseSerializers = $config['baseSerializers'] ?? [
            ArraySerializer::class,
            DataSerializer::class,
            DateTimeSerializer::class,
            BackedEnumSerializer::class,
            ScalarTypeSerializer::class,
        ];

        if (
            !is_array($baseSerializers)
            || array_any(
                $baseSerializers,
               static fn (mixed $baseSerializer): bool =>
                     !is_string($baseSerializer)
                     || !is_subclass_of($baseSerializer, Serializer::class)
            )
        ) {
            throw DataConfigurationException::invalidBaseSerializers();
        }
        /** @var array<array-key, class-string<Serializer>> $baseSerializers */

        return self::$instance = new self(
            baseSerializers: $baseSerializers
        );
    }
}
