<?php

namespace Nuxtifyts\PhpDto\Serializers;

use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Helper\ReflectionPropertyHelper;
use ReflectionProperty;
use ArrayAccess;

class ScalarTypeSerializer extends Serializer
{
    /** @var array<string> */
    protected const array TYPES = [
        'double',
        'float',
        'int',
        'integer',
        'string',
        'bool',
        'boolean',
        'null'
    ];

    public static function isSupported(
        ReflectionProperty $property,
        object $object
    ): bool {
        return count(array_intersect(
            ReflectionPropertyHelper::getPropertyTypes($property),
            self::TYPES
        )) > 0;
    }

    /**
     * @inheritDoc
     */
    public function serialize(
        ReflectionProperty $property,
        object $object
    ): array {
        return [
            $property->getName() => $property->getValue($object)
        ];
    }

    /**
     * @inheritDoc
     */
    public function deserialize(
        ReflectionProperty $property,
        ArrayAccess|array $data
    ): mixed {
        $value = $data[$property->getName()] ?? null;
        $types = ReflectionPropertyHelper::getPropertyTypes($property);

        if ($value === null && in_array('null', $types)) {
            return null;
        }

        if (
            array_any(
                $types,
                static fn(string $type) => settype($value, $type)
            )
        ) {
            return $value;
        }

        throw new DeserializeException('Could not deserialize scalar type');
    }
}
