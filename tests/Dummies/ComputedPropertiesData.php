<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Computed;
use Nuxtifyts\PhpDto\Data;

final readonly class ComputedPropertiesData extends Data
{
    #[Computed]
    public string $c;

    public function __construct(
        public string $a,
        public string $b,
    ) {
        $this->c = $this->a . $this->b;
    }
}
