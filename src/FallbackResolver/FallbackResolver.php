<?php

namespace Nuxtifyts\PhpDto\FallbackResolver;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Exceptions\FallbackResolverException;

interface FallbackResolver
{
    /**
     * @param array<string, mixed> $rawData
     *
     * @throws FallbackResolverException
     */
    public static function resolve(array $rawData, PropertyContext $property): mixed;
}
