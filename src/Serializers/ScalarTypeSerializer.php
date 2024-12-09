<?php

namespace Nuxtifyts\PhpDto\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use ArrayAccess;

class ScalarTypeSerializer extends Serializer
{
    /**
     * @inheritDoc
     */
    public static function supportedTypes(): array
    {
        return [
            Type::BOOLEAN,
            Type::FLOAT,
            Type::INT,
            Type::STRING
        ];
    }

    /**
     * @inheritDoc
     */
    public function serialize(
        PropertyContext $property,
        object $object
    ): array {
        return [
            $property->propertyName => $property->getValue($object)
        ];
    }

    /**
     * @inheritDoc
     */
    public function deserialize(
        PropertyContext $property,
        ArrayAccess|array $data
    ): mixed {
        $value = $data[$property->propertyName] ?? null;

        if (
            array_any(
                array_column($property->types, 'value'),
                static fn(string $type) => settype($value, $type)
            )
        ) {
            if ($value !== null) {
                return $value;
            } elseif ($property->isNullable) {
                return null;
            }
        }

        throw new DeserializeException('Could not deserialize scalar type');
    }
}
