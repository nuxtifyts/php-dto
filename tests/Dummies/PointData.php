<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;

final readonly class PointData extends Data
{
    public function __construct(
        public float $x,
        public float $y
    ) {
    }
}
