<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;

final readonly class UserGroupData extends Data
{
    /**
     * @param list<int> $userIds
     */
    public function __construct(
        public string $name,
        #[ArrayOfScalarTypes(Type::INT)]
        public array $userIds
    ) {
    }
}
