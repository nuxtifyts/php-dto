<?php

namespace Nuxtifyts\PhpDto\DataRefiners;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\InvalidRefiner;

interface DataRefiner
{
    /**
     * @throws InvalidRefiner
     */
    public function refine(mixed $value, PropertyContext $property): mixed;
}
