<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\FallbackResolvers;

use Nuxtifyts\PhpDto\Tests\Dummies\PointData;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\FallbackResolver\FallbackResolver;

class DummyPointsFallbackResolver implements FallbackResolver
{
    /**
     * @return list<PointData>
     */
    public static function resolve(array $rawData, PropertyContext $property): array
    {
        return [
            new PointData(1, 2),
            new PointData(3, 4),
            new PointData(5, 6),
        ];
    }
}
