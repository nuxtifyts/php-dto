<?php

namespace Nuxtifyts\PhpDto\Serializers;

use Nuxtifyts\PhpDto\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;
use Nuxtifyts\PhpDto\Concerns\SerializesArrayOfItems;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

class MixedDataSerializer extends Serializer implements SerializesArrayOfItemsContract
{
    use SerializesArrayOfItems;

    /**
     * @return list<Type>
     */
    public static function supportedTypes(): array
    {
        return [
            Type::MIXED,
        ];
    }

    /**
     * @throws SerializeException
     */
    protected function serializeItem(mixed $item, PropertyContext $property, object $object): mixed
    {
        return match(true) {
            is_null($item) && $property->isNullable => null,

            default => is_null($item)
                ? throw new SerializeException('Could not serialize array of BaseDataContract items')
                : $item,
        };
    }

    /**
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): mixed
    {
        return match(true) {
            is_null($item) && $property->isNullable => null,

            default => is_null($item)
                ? throw new DeserializeException('Could not deserialize array of BaseDataContract items')
                : $item,
        };
    }
}
