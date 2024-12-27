<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\FallbackResolvers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\FallbackResolver\FallbackResolver;
use Nuxtifyts\PhpDto\Tests\Dummies\UserData;

class DummyUserFallbackResolver implements FallbackResolver
{
    public static function resolve(array $rawData, PropertyContext $property): mixed
    {
        if (array_key_exists($property->propertyName, $rawData)) {
            return $rawData[$property->propertyName];
        }

        return new UserData(
            'John',
            'Doe'
        );
    }
}
