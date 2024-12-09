<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

abstract class Serializer
{
    final public function __construct() {}

    /**
     * @return list<Type>
     */
    abstract public static function supportedTypes(): array;

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    abstract public function serialize(
        PropertyContext $property,
        object $object
    ): array;

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed> $data
     *
     * @throws DeserializeException
     */
    abstract public function deserialize(
        PropertyContext $property,
        array|ArrayAccess $data
    ): mixed;
}
