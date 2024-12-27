<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;

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
     * @return list<Serializer>
     */
    protected static function serializersList(): array
    {
        return [];
    }

    /**
     * @return list<Serializer>
     */
    protected function resolveSerializers(): array
    {
        return [];
    }
}
