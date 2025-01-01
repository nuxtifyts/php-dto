<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Normalizers;

use Nuxtifyts\PhpDto\Tests\Dummies\NonData\Human;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;

final readonly class HumanToPersonNormalizer extends Normalizer
{
    /**
     * @return array<string, mixed>|false
     */
    public function normalize(): array|false
    {
        return $this->value instanceof Human
            ? [
                'firstName' => $this->value->name,
                'lastName' => $this->value->surname
            ]
            : false;
    }
}
