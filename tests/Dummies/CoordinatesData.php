<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;

final readonly class CoordinatesData extends Data
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?float $radius = null
    ) {
    }
}
