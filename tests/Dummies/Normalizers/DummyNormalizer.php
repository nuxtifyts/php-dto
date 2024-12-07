<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Normalizers;

use Nuxtifyts\PhpDto\Normalizers\Normalizer;

final readonly class DummyNormalizer extends Normalizer
{
    public function normalize(): false
    {
        return false;
    }
}
