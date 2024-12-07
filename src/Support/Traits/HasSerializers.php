<?php

namespace Nuxtifyts\PhpDto\Support\Traits;

use Nuxtifyts\PhpDto\Exceptions\UnknownPropertyException;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use ReflectionProperty;

trait HasSerializers
{
    /**
     * @throws UnknownPropertyException
     */
    protected function resolveSerializer(
        ReflectionProperty $property,
        object $object
    ): Serializer {
        foreach (self::serializers() as $serializer) {
            if ($serializer::isSupported($property, $object)) {
                return new $serializer;
            }
        }

        throw UnknownPropertyException::from(
            $property->getName(),
            $object
        );
    }

    /**
     * @return list<class-string<Serializer>>
     */
    protected static function serializers(): array
    {
        return [
            ScalarTypeSerializer::class
        ];
    }
}
