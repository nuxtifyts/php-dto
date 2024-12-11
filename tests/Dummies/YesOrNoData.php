<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;

final readonly class YesOrNoData extends Data
{
    public function __construct(
        public YesNoBackedEnum $yesNo
    ) {
    }
}
