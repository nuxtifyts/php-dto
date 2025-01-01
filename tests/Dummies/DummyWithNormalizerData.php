<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\Class\WithNormalizer;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\DummyNormalizer;

#[WithNormalizer(DummyNormalizer::class)]
final readonly class DummyWithNormalizerData extends Data
{
    public function __construct() {
    }
}
