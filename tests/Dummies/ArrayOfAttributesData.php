<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use DateTimeImmutable;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfBackedEnums;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfDateTimes;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;

final readonly class ArrayOfAttributesData extends Data
{
    /**
     * @param array<array-key, int> $arrayOfIntegers
     * @param array<array-key, string> $arrayOfStrings
     * @param array<array-key, float> $arrayOfFloats
     * @param array<array-key, bool> $arrayOfBooleans
     * @param array<array-key, YesNoBackedEnum> $arrayOfBackedEnums
     * @param array<array-key, DateTimeImmutable> $arrayOfDateTimes
     * @param array<array-key, PersonData> $arrayOfPersonData
     */
    public function __construct(
        #[ArrayOfScalarTypes(Type::INT)]
        public ?array $arrayOfIntegers = null,
        #[ArrayOfScalarTypes(Type::STRING)]
        public ?array $arrayOfStrings = null,
        #[ArrayOfScalarTypes(Type::FLOAT)]
        public ?array $arrayOfFloats = null,
        #[ArrayOfScalarTypes(Type::BOOLEAN)]
        public ?array $arrayOfBooleans = null,
        #[ArrayOfBackedEnums(YesNoBackedEnum::class)]
        public ?array $arrayOfBackedEnums = null,
        #[ArrayOfDateTimes(DateTimeImmutable::class)]
        public ?array $arrayOfDateTimes = null,
        #[ArrayOfData(PersonData::class)]
        public ?array $arrayOfPersonData = null,
    ) {
    }
}
