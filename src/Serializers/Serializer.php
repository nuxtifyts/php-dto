<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

abstract class Serializer
{
    final public function __construct()
    {
    }

    /**
     * @return list<Type>
     */
    abstract public static function supportedTypes(): array;

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    public function serialize(PropertyContext $property, object $object): array
    {
        $value = $property->getValue($object);

        return [
            $property->propertyName => $this->serializeItem($value, $property, $object)
        ];
    }

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed> $data
     *
     * @throws DeserializeException
     */
    public function deserialize(PropertyContext $property, array|ArrayAccess $data): mixed
    {
        $value = $data[$property->propertyName] ?? null;

        return $this->deserializeItem($value, $property);
    }

    /**
     * @throws SerializeException
     */
    abstract protected function serializeItem(mixed $item, PropertyContext $property, object $object): mixed;

    /**
     * @throws DeserializeException
     */
    abstract protected function deserializeItem(mixed $item, PropertyContext $property): mixed;
}
