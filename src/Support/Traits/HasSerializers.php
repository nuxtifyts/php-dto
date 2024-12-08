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
     *
     * @return list<Serializer>
     */
    protected function resolveSerializers(
        ReflectionProperty $property,
        object $object
    ): array {
        $serializers = array_values(array_filter(array_map(
                /** @param class-string<Serializer> $serializer */
            static fn (string $serializer): ?Serializer =>
                $serializer::isSupported($property, $object)
                    ? new $serializer
                    : null,
            self::serializers()
        )));

        return $serializers ?:  throw UnknownPropertyException::from(
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
