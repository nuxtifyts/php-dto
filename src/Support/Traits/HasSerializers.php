<?php

namespace Nuxtifyts\PhpDto\Support\Traits;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Exceptions\UnknownPropertyException;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use ReflectionProperty;

trait HasSerializers
{
    /**
     * @return list<Serializer>
     *
     * @throws UnknownTypeException
     */
    protected function getSerializers(
        PropertyContext $propertyContext
    ): array {
        return array_values(array_filter(array_map(
            /** @param class-string<Serializer> $serializer */
            static fn (string $serializer): ?Serializer =>
                !empty(array_intersect(
                    array_column($propertyContext->types, 'value'),
                    array_column($serializer::supportedTypes(), 'value')
                )) ? new $serializer : null,
            self::serializersList()
        ))) ?: throw UnknownTypeException::from(...$propertyContext->types);
    }

    /**
     * @return list<class-string<Serializer>>
     */
    protected static function serializersList(): array
    {
        return [
            ScalarTypeSerializer::class
        ];
    }
}
