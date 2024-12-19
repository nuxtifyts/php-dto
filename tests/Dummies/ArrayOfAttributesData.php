<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;

final readonly class ArrayOfAttributesData extends Data
{
    /**
     * @param list<int> $arrayOrIntegers
     */
    public function __construct(
        #[ArrayOfScalarTypes(Type::INT)]
        public array $arrayOrIntegers,
    ) {
    }
}
