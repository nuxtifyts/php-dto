<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;

final readonly class UnionMultipleTypeData extends Data
{
    public function __construct(
        public string|int|float $value,
        public YesNoBackedEnum|bool $yesOrNo
    ) {
    }
}
