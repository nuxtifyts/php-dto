<?php

namespace Nuxtifyts\PhpDto\Serializers;

use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Serializers\Contracts\SerializesArrayOfItems;

class ArraySerializer extends Serializer
{
    public static function supportedTypes(): array
    {
        return [
            Type::ARRAY
        ];
    }

    /**
     * @return ?array<array-key, mixed>
     *
     * @throws SerializeException
     */
    protected function serializeItem(mixed $item, PropertyContext $property, object $object): ?array
    {
        if ($item === null && $property->isNullable) {
            return null;
        }

        if (is_array($item)) {
            foreach ($property->getFilteredTypeContexts(...self::supportedTypes()) as $typeContext) {
                try {
                    foreach ($typeContext->subTypeSerializers() as $serializer) {
                        try {
                            if ($serializer instanceof SerializesArrayOfItems) {
                                $serializedValue = $serializer->serializeArrayOfItems($property, $object);

                                if (array_key_exists($property->propertyName, $serializedValue)) {
                                    return $serializedValue[$property->propertyName];
                                }
                            }
                        } catch (Exception) {
                        }
                    }
                } catch (Exception) {
                }
            }
        }

        throw SerializeException::unableToSerializeArrayItem();
    }

    /**
     * @return ?array<array-key, mixed>
     *
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): ?array
    {
        if (is_array($item)) {
            foreach ($property->getFilteredTypeContexts(...self::supportedTypes()) as $typeContext) {
                try {
                    foreach ($typeContext->subTypeSerializers() as $serializer) {
                        try {
                            if ($serializer instanceof SerializesArrayOfItems) {
                                // @phpstan-ignore-next-line
                                return $serializer->deserializeArrayOfItems($property, $item);
                            }
                        } catch (Exception) {
                        }
                    }
                } catch (Exception) {
                }
            }
        }

        return is_null($item) && $property->isNullable
            ? null
            : throw DeserializeException::unableToDeserializeArrayItem();
    }
}
