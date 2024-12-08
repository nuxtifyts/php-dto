<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Support;

use Nuxtifyts\PhpDto\Exceptions\UnknownPropertyException;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use ReflectionProperty;

final class HasSerializersDummy
{
    use HasSerializers;

    /**
     * @throws UnknownPropertyException
     *
     * @return list<Serializer>
     */
    public function testResolveSerializers(
        ReflectionProperty $property,
        object $object
    ): array {
        return $this->resolveSerializers($property, $object);
    }

    /**
     * @return list<class-string<Serializer>>
     */
    protected static function serializers(): array
    {
        return [];
    }
}
