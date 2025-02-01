<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\Class\Lazy;
use Nuxtifyts\PhpDto\Data;

#[Lazy]
final readonly class LazyDummyData extends Data
{
    public function __construct(
        public string $propertyA,
        public string $propertyB,
    ) {
    }
}
