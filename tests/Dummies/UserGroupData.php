<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\ArrayOfData;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;

final readonly class UserGroupData extends Data
{
    /**
     * @param array<array-key, int|UserData> $users
     */
    public function __construct(
        public string $name,
        #[ArrayOfScalarTypes(Type::INT)]
        #[ArrayOfData(UserData::class)]
        public array $users
    ) {
    }
}
