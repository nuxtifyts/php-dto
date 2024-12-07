<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;

final readonly class UnionTypedData extends Data
{
    public function __construct(
        public int|string|null $value
    ) {
    }
}
