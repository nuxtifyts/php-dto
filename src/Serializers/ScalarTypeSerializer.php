<?php

namespace Nuxtifyts\PhpDto\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Serializers\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;
use Nuxtifyts\PhpDto\Serializers\Concerns\SerializesArrayOfItems;

class ScalarTypeSerializer extends Serializer implements SerializesArrayOfItemsContract
{
    use SerializesArrayOfItems;

    public static function supportedTypes(): array
    {
        return [
            Type::INT,
            Type::FLOAT,
            Type::STRING,
            Type::BOOLEAN,
        ];
    }

    /**
     * @return list<Type>
     */
    private static function getScalarTypeFromProperty(
        PropertyContext $property
    ): array {
        return array_map(
            static fn (TypeContext $typeContext) => $typeContext->type,
            $property->getFilteredTypeContexts(...self::supportedTypes())
                ?: $property->getFilteredSubTypeContexts(...self::supportedTypes())
        );
    }

    /**
     * @throws SerializeException
     */
    protected function serializeItem(mixed $item, PropertyContext $property, object $object): mixed
    {
        return match(true) {
            is_null($item) && $property->isNullable => null,

            is_bool($item),
            is_float($item),
            is_int($item),
            is_string($item) => $item,

            default => throw SerializeException::unableToSerializeBackedEnumItem()
        };
    }

    /**
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): mixed
    {
        return match(true) {
            is_null($item) => $property->isNullable
                ? null
                : throw DeserializeException::propertyIsNotNullable(),

            array_any(
                array_column(self::getScalarTypeFromProperty($property), 'value'),
                static fn (string $type) => settype($item, $type)
            ) => $item,

            default => throw DeserializeException::unableToDeserializeScalarTypeItem()
        };
    }
}
