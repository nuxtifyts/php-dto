<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use ReflectionProperty;

abstract class Serializer
{
    final public function __construct() {}

    abstract public static function isSupported(
        ReflectionProperty $property,
        object $object,
    ): bool;

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    abstract public function serialize(
        ReflectionProperty $property,
        object $object
    ): array;

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed> $data
     *
     * @throws DeserializeException
     */
    abstract public function deserialize(
        ReflectionProperty $property,
        array|ArrayAccess $data
    ): mixed;
}
