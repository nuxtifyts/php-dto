<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Serializers\Concerns\HasSerializers;
use Nuxtifyts\PhpDto\Serializers\DateTimeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;

class HasSerializersDummyClass
{
    use HasSerializers;

    /**
     * @throws UnknownTypeException
     *
     * @return list<Serializer>
     */
    public static function testGetSerializersFromPropertyContext(PropertyContext $propertyContext): array
    {
        return new self()->getSerializersFromPropertyContext($propertyContext);
    }

    /**
     * @return list<class-string<Serializer>>
     */
    protected static function serializersList(): array
    {
        return [
            DateTimeSerializer::class
        ];
    }

    /**
     * @return list<Serializer>
     */
    protected function resolveSerializers(): array
    {
        return [];
    }
}
