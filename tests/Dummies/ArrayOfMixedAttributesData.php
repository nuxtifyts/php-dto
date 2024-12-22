<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\ArrayOfBackedEnums;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;

final readonly class ArrayOfMixedAttributesData extends Data
{
    /**
     * @param ?array<array-key, int|YesNoBackedEnum>  $arrayOfIntegersOrBackedEnums
     */
    public function __construct(
        #[ArrayOfScalarTypes(Type::INT)]
        #[ArrayOfBackedEnums(YesNoBackedEnum::class)]
        public ?array $arrayOfIntegersOrBackedEnums = null
    ) {
    }
}
