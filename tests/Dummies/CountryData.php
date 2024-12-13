<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;

final readonly class CountryData extends Data
{
    public function __construct(
        public string $code,
        public string $name
    ) {
    }
}
