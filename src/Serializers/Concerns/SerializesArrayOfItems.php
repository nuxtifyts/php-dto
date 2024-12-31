<?php

namespace Nuxtifyts\PhpDto\Serializers\Concerns;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Serializers\Serializer;

/**
 * @mixin Serializer
 */
trait SerializesArrayOfItems
{
    /**
     * @return array<string, ?array<array-key, mixed>>
     *
     * @throws SerializeException
     */
    public function serializeArrayOfItems(
        PropertyContext $property,
        object $object
    ): array {
        $value = $property->getValue($object);

        return [
            $property->propertyName => match(true) {
                is_null($value) && $property->isNullable => null,

                is_array($value) => array_map(
                    fn (mixed $item) => $this->serializeItem($item, $property, $object),
                    $value
                ),

                default => throw SerializeException::unableToSerializeArrayItem()
            }
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return ?array<array-key, mixed>
     *
     * @throws DeserializeException
     */
    public function deserializeArrayOfItems(
        PropertyContext $property,
        array $data
    ): ?array {
        return array_map(
            fn (mixed $item) => $this->deserializeItem($item, $property),
            $data
        );
    }
}
