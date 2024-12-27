<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\Property\CipherTarget;
use Nuxtifyts\PhpDto\Attributes\Property\DefaultsTo;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Dummies\FallbackResolvers\DummyPointsFallbackResolver;

final readonly class PointGroupData extends Data
{
    /**
     * @param array<array-key, PointData> $points
     */
    public function __construct(
        #[CipherTarget]
        public string $key,
        #[ArrayOfData(PointData::class)]
        #[DefaultsTo(DummyPointsFallbackResolver::class)]
        public array $points
    ) {
    }
}
