<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Normalizers;

use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Nuxtifyts\PhpDto\Normalizers\ArrayNormalizer;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Normalizer::class)]
#[CoversClass(ArrayNormalizer::class)]
#[UsesClass(PersonData::class)]
final class ArrayNormalizerTest extends UnitCase
{
    #[Test]
    public function will_return_false_when_value_is_not_an_array(): void
    {
        $normalizer = new ArrayNormalizer('test', PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_false_when_value_is_a_list(): void
    {
        $normalizer = new ArrayNormalizer(['test'], PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_false_when_key_is_not_a_string(): void
    {
        $normalizer = new ArrayNormalizer([1 => 'test'], PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_normalized_array(): void
    {
        $normalizer = new ArrayNormalizer(['key' => 'value'], PersonData::class);

        self::assertEquals(['key' => 'value'], $normalizer->normalize());
    }
}
